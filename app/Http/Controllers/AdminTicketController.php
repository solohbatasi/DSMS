<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Student_ef_list;
use App\Models\TicketDownload;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AdminTicketController extends Controller
{
    /**
     * Show ticket management page for a specific student
     */
    public function manageTickets($studentId)
    {
        // Get student with all necessary relationships
        $student = Student::with(['efLists' => function($query) {
            $query->where('status', 'active')
                  ->with('course');
        }])->findOrFail($studentId);

        // Get recent tickets (last 30 days)
        $recent_tickets = TicketDownload::with(['efList.course'])
            ->where('student_id', $student->id)
            ->where('download_time', '>=', now()->subDays(30))
            ->orderBy('download_time', 'desc')
            ->take(20)
            ->get();

        return view('admin.tickets.manage', [
            'student' => $student,
            'ef_lists' => $student->efLists,
            'recent_tickets' => $recent_tickets,
            'today' => Carbon::today()->format('Y-m-d')
        ]);
    }

    /**
     * Download ticket for a student (admin/reception action)
     */
    public function downloadTicketForStudent(Request $request, $studentId)
    {
        $request->validate([
            'ef_id' => 'required|exists:student_ef_lists,id',
            'session_type' => 'required|in:practical,theoretical',
            'course_id' => 'required|exists:courses,id',
            'session_date' => 'required|date|before_or_equal:today'
        ]);

        $student = Student::findOrFail($studentId);

        // Check if student owns this EF list
        $ef_list = Student_ef_list::where('id', $request->ef_id)
            ->where('student_id', $student->id)
            ->with('course')
            ->first();

        if (!$ef_list) {
            return back()->with('error', 'Invalid course selection.');
        }

        $course = $ef_list->course;
        if (!$course) {
            return back()->with('error', 'Course not found.');
        }

        // Parse the selected date
        $sessionDate = Carbon::parse($request->session_date)->startOfDay();
        $today = Carbon::today();

        // Validate date is not in the future
        if ($sessionDate->greaterThan($today)) {
            return back()->with('error', 'Cannot download tickets for future dates.');
        }

        // Get the enrollment date (when the EF list was created)
        $enrollmentDate = Carbon::parse($ef_list->created_at)->startOfDay();

        // Validate date is not before enrollment
        if ($sessionDate->lessThan($enrollmentDate)) {
            return back()->with('error', 'Session date cannot be before the student\'s enrollment date (' . $enrollmentDate->format('M d, Y') . ').');
        }

        // Check if ticket already downloaded for this session type on the selected date
        $existing_ticket = TicketDownload::where('student_id', $student->id)
            ->where('ef_id', $request->ef_id)
            ->where('session_type', $request->session_type)
            ->whereDate('download_time', $sessionDate)
            ->first();

        if ($existing_ticket) {
            return back()->with('error', 'Ticket already downloaded for this session type on ' . $sessionDate->format('M d, Y') . '.');
        }

        // Count downloads for this session type on the selected date
        $date_session_count = TicketDownload::where('student_id', $student->id)
            ->where('ef_id', $request->ef_id)
            ->where('session_type', $request->session_type)
            ->whereDate('download_time', $sessionDate)
            ->count();

        $max_sessions = $request->session_type === 'practical'
            ? $course->practical_sessions
            : $course->theoretical_sessions;

        if ($date_session_count >= $max_sessions) {
            return back()->with('error', "Maximum {$request->session_type} sessions reached for " . $sessionDate->format('M d, Y') . ".");
        }

        // Only initialize if this is the very first download (both attended and remaining are 0)
        // But only for practical sessions (theoretical sessions are initialized differently)
        if ($request->session_type === 'practical' && $ef_list->attended_practical == 0 && $ef_list->remaining_practical == 0) {
            $ef_list->remaining_practical = $ef_list->total_class; // student allocation
            $ef_list->save();
        }

        if ($request->session_type === 'theoretical' && $ef_list->attended_theoretical == 0 && $ef_list->remaining_theoretical == 0) {
            $ef_list->remaining_theoretical = $course->theoretical_sessions;
            $ef_list->save();
        }

        // Check remaining sessions before download
        if ($request->session_type === 'practical' && $ef_list->remaining_practical <= 0) {
            return back()->with('error', 'Student has used all practical sessions.');
        }

        if ($request->session_type === 'theoretical' && $ef_list->remaining_theoretical <= 0) {
            return back()->with('error', 'Student has used all theoretical sessions.');
        }

        // Use database transaction to ensure data consistency
        DB::beginTransaction();

        try {
            // Create ticket download record with custom date
            $ticket = TicketDownload::create([
                'student_id' => $student->id,
                'ef_id' => $request->ef_id,
                'download_time' => $sessionDate->copy()->setTime(now()->hour, now()->minute, now()->second),
                'session_type' => $request->session_type,
                'downloaded_by' => Auth::user()->id, // Track who downloaded
                'downloaded_by_role' => Auth::user()->role,
                'session_date' => $sessionDate // Store the actual session date
            ]);

            // Update attendance counts based on session type
            if ($request->session_type === 'practical') {
                // Increment attended_practical and decrement remaining_practical
                $ef_list->update([
                    'attended_practical' => $ef_list->attended_practical + 1,
                    'remaining_practical' => $ef_list->remaining_practical - 1
                ]);
            } else {
                // Increment attended_theoretical and decrement remaining_theoretical
                $ef_list->update([
                    'attended_theoretical' => $ef_list->attended_theoretical + 1,
                    'remaining_theoretical' => $ef_list->remaining_theoretical - 1
                ]);
            }

            // Commit the transaction
            DB::commit();

            // Generate QR code data
            $qr_data = json_encode([
                'ticket_id' => $ticket->id,
                'student_id' => $student->id,
                'ef_no' => $ef_list->ef_no,
                'session_type' => $request->session_type,
                'session_date' => $sessionDate->format('Y-m-d'),
                'timestamp' => $ticket->download_time->timestamp,
                'attended_practical' => $ef_list->attended_practical,
                'remaining_practical' => $ef_list->remaining_practical,
                'attended_theoretical' => $ef_list->attended_theoretical,
                'remaining_theoretical' => $ef_list->remaining_theoretical,
                'downloaded_by' => Auth::user()->name,
                'is_admin' => true
            ]);

            // Generate QR code as base64
            $qr_code = $this->generateQRCode($qr_data);

            return view('tickets.print', [
                'ticket' => $ticket,
                'student' => $student,
                'ef_list' => $ef_list,
                'course' => $course,
                'qr_data' => $qr_data,
                'qr_code_base64' => $qr_code,
                'session_type' => $request->session_type,
                'session_date' => $sessionDate->format('M d, Y'),
                'downloaded_by' => Auth::user()->name,
                'is_admin_download' => true
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            logger()->error('Admin ticket download failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to download ticket. Please try again.');
        }
    }

    /**
     * Redownload ticket (admin version) - now allows any date
     */
    public function redownloadTicket($ticketId)
    {
        // Find the existing ticket
        $ticket = TicketDownload::with(['efList.course', 'student'])
            ->findOrFail($ticketId);

        // Generate QR code data (without updating attendance counts)
        $qr_data = json_encode([
            'ticket_id' => $ticket->id,
            'student_id' => $ticket->student_id,
            'ef_no' => $ticket->efList->ef_no,
            'session_type' => $ticket->session_type,
            'session_date' => $ticket->session_date ? Carbon::parse($ticket->session_date)->format('Y-m-d') : $ticket->download_time->format('Y-m-d'),
            'timestamp' => $ticket->download_time->timestamp,
            'redownloaded' => true,
            'attended_practical' => $ticket->efList->attended_practical,
            'remaining_practical' => $ticket->efList->remaining_practical,
            'attended_theoretical' => $ticket->efList->attended_theoretical,
            'remaining_theoretical' => $ticket->efList->remaining_theoretical,
            'redownloaded_by' => Auth::user()->name,
            'is_admin' => true
        ]);

        // Generate QR code as base64
        $qr_code = $this->generateQRCode($qr_data);

        return view('tickets.print', [
            'ticket' => $ticket,
            'student' => $ticket->student,
            'ef_list' => $ticket->efList,
            'course' => $ticket->efList->course,
            'qr_data' => $qr_data,
            'qr_code_base64' => $qr_code,
            'session_type' => $ticket->session_type,
            'session_date' => $ticket->session_date ? Carbon::parse($ticket->session_date)->format('M d, Y') : $ticket->download_time->format('M d, Y'),
            'downloaded_by' => Auth::user()->name,
            'is_admin_download' => true
        ]);
    }

    /**
     * Preview ticket (admin version)
     */
    public function previewTicket($ticketId)
    {
        // Find the existing ticket
        $ticket = TicketDownload::with(['efList.course', 'student'])
            ->findOrFail($ticketId);

        // Generate QR code data
        $qr_data = json_encode([
            'ticket_id' => $ticket->id,
            'student_id' => $ticket->student_id,
            'ef_no' => $ticket->efList->ef_no,
            'session_type' => $ticket->session_type,
            'session_date' => $ticket->session_date ? Carbon::parse($ticket->session_date)->format('Y-m-d') : $ticket->download_time->format('Y-m-d'),
            'timestamp' => $ticket->download_time->timestamp,
            'preview' => true,
            'previewed_by' => Auth::user()->name,
            'is_admin' => true
        ]);

        // Generate QR code as base64
        $qr_code = $this->generateQRCode($qr_data);

        return view('tickets.print', [
            'ticket' => $ticket,
            'student' => $ticket->student,
            'ef_list' => $ticket->efList,
            'course' => $ticket->efList->course,
            'qr_data' => $qr_data,
            'qr_code_base64' => $qr_code,
            'session_type' => $ticket->session_type,
            'session_date' => $ticket->session_date ? Carbon::parse($ticket->session_date)->format('M d, Y') : $ticket->download_time->format('M d, Y'),
            'downloaded_by' => Auth::user()->name,
            'is_admin_download' => true
        ]);
    }

    /**
     * Generate QR Code
     */
    private function generateQRCode($data)
    {
        try {
            // Use your existing QR code generation logic
            // If you have the QR code package installed
            if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
                $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)
                    ->generate($data);
                return 'data:image/png;base64,' . base64_encode($qrCode);
            }

            // Fallback
            return 'data:image/svg+xml;base64,' . base64_encode(
                '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
                    <rect width="200" height="200" fill="#f3f4f6"/>
                    <text x="100" y="100" text-anchor="middle" fill="#6b7280">QR Code</text>
                </svg>'
            );
        } catch (\Exception $e) {
            return 'data:image/svg+xml;base64,' . base64_encode(
                '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
                    <rect width="200" height="200" fill="#f3f4f6"/>
                    <text x="100" y="100" text-anchor="middle" fill="#6b7280">QR Code Error</text>
                </svg>'
            );
        }
    }
    /**
 * Add previous unrecorded classes for a student
 */
public function addPreviousClasses(Request $request, $studentId)
{
    $request->validate([
        'ef_id' => 'required|exists:student_ef_lists,id',
        'session_type' => 'required|in:practical,theoretical',
        'course_id' => 'required|exists:courses,id',
        'previous_classes_count' => 'required|integer|min:1|max:100',
        'start_date' => 'nullable|date|before_or_equal:today',
        'end_date' => 'nullable|date|before_or_equal:today|after_or_equal:start_date',
        'notes' => 'nullable|string|max:500'
    ]);

    $student = Student::findOrFail($studentId);

    // Check if student owns this EF list
    $ef_list = Student_ef_list::where('id', $request->ef_id)
        ->where('student_id', $student->id)
        ->with('course')
        ->first();

    if (!$ef_list) {
        return back()->with('error', 'Invalid course selection.');
    }

    $course = $ef_list->course;
    if (!$course) {
        return back()->with('error', 'Course not found.');
    }

    // Check remaining sessions
    $remaining = $request->session_type === 'practical'
        ? $ef_list->remaining_practical
        : $ef_list->remaining_theoretical;

    if ($remaining < $request->previous_classes_count) {
        return back()->with('error', "Cannot add {$request->previous_classes_count} classes. Only {$remaining} sessions remaining.");
    }

    // Use database transaction
    DB::beginTransaction();

    try {
        // Determine dates for the tickets
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::today();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::today();

        // If only one date provided, use it for all
        if ($request->start_date && !$request->end_date) {
            $endDate = $startDate;
        }

        // Generate dates between start and end
        $dates = [];
        if ($startDate->eq($endDate)) {
            // Single date - add multiple tickets on same date with time offsets
            for ($i = 0; $i < $request->previous_classes_count; $i++) {
                $dates[] = $startDate->copy()->setTime(9 + $i, 0, 0); // Different hours
            }
        } else {
            // Date range - spread tickets across dates
            $dateRange = [];
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                $dateRange[] = $currentDate->copy();
                $currentDate->addDay();
            }

            // Distribute tickets across available dates
            $totalDates = count($dateRange);
            for ($i = 0; $i < $request->previous_classes_count; $i++) {
                $dateIndex = $i % $totalDates;
                $dates[] = $dateRange[$dateIndex]->copy()->setTime(9 + ($i * 2 % 8), 0, 0);
            }
        }

        // Create ticket records
        $tickets = [];
        foreach ($dates as $index => $dateTime) {
            $ticket = TicketDownload::create([
                'student_id' => $student->id,
                'ef_id' => $request->ef_id,
                'download_time' => $dateTime,
                'session_type' => $request->session_type,
                'downloaded_by' => Auth::user()->id,
                'downloaded_by_role' => Auth::user()->role . ' (bulk add)',
                'session_date' => $dateTime->copy()->startOfDay(),
                'notes' => $request->notes ?: 'Previous unrecorded classes added',
                'is_bulk_add' => true
            ]);
            $tickets[] = $ticket;
        }

        // Update attendance counts
        if ($request->session_type === 'practical') {
            $ef_list->update([
                'attended_practical' => $ef_list->attended_practical + $request->previous_classes_count,
                'remaining_practical' => $ef_list->remaining_practical - $request->previous_classes_count
            ]);
        } else {
            $ef_list->update([
                'attended_theoretical' => $ef_list->attended_theoretical + $request->previous_classes_count,
                'remaining_theoretical' => $ef_list->remaining_theoretical - $request->previous_classes_count
            ]);
        }

        DB::commit();

        return redirect()->route('admin.students.tickets.manage', ['student' => $student->id])
            ->with('success', "Successfully added {$request->previous_classes_count} previous {$request->session_type} classes for {$student->name}.");

    } catch (\Exception $e) {
        DB::rollback();
        logger()->error('Add previous classes failed: ' . $e->getMessage());
        return back()->with('error', 'Failed to add previous classes. Please try again.');
    }
}
}
