<?php

namespace App\Http\Controllers;

use App\Models\TicketDownload;
use App\Models\Student;
use App\Models\Student_ef_list;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student profile not found.');
        }

        // Method 1: Direct query to check data
        $ef_lists_raw = \DB::table('student_ef_lists')
            ->where('student_id', $student->id)
            ->where('status', 'active')
            ->get();

        \Log::info('Raw EF Lists:', $ef_lists_raw->toArray());

        // Method 2: Eager load with explicit foreign keys
        $ef_lists = Student_ef_list::with(['course' => function($query) {
                $query->select('id', 'course', 'practical_sessions', 'theoretical_sessions');
            }])
            ->where('student_id', $student->id)
            ->where('status', 'active')
            ->get();

        // Method 3: Manual loading if eager load fails
        foreach ($ef_lists as $ef) {
            if (!$ef->course) {
                $ef->course = Course::find($ef->course_id);
            }
        }

        \Log::info('Processed EF Lists:', [
            'count' => $ef_lists->count(),
            'data' => $ef_lists->map(function($ef) {
                return [
                    'id' => $ef->id,
                    'ef_no' => $ef->ef_no,
                    'course_id' => $ef->course_id,
                    'course_name' => $ef->course ? $ef->course->course : 'No course found',
                    'practical_sessions' => $ef->course ? $ef->course->practical_sessions : 0,
                    'theoretical_sessions' => $ef->course ? $ef->course->theoretical_sessions : 0
                ];
            })->toArray()
        ]);

        $today_downloads = TicketDownload::where('student_id', $student->id)
            ->whereDate('download_time', Carbon::today())
            ->get();

        return view('tickets.index', compact('student', 'ef_lists', 'today_downloads'));
    }

    public function download(Request $request)
    {
        $request->validate([
            'ef_id' => 'required|exists:student_ef_lists,id',
            'session_type' => 'required|in:practical,theoretical'
        ]);

        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return back()->with('error', 'Student profile not found.');
        }

        // Check if student owns this EF
        $ef_list = Student_ef_list::where('id', $request->ef_id)
            ->where('student_id', $student->id)
            ->first();

        if (!$ef_list) {
            return back()->with('error', 'Invalid course selection.');
        }

        // Check if ticket already downloaded for this session type today
        $existing_ticket = TicketDownload::where('student_id', $student->id)
            ->where('ef_id', $request->ef_id)
            ->where('session_type', $request->session_type)
            ->whereDate('download_time', Carbon::today())
            ->first();

        if ($existing_ticket) {
            return back()->with('error', 'You have already downloaded a ticket for this session type today.');
        }

        // Check session limits
        if (!$this->checkSessionLimit($ef_list, $request->session_type)) {
            return back()->with('error', 'No sessions remaining for this session type.');
        }

        // Create ticket download record
        $ticket = TicketDownload::create([
            'student_id' => $student->id,
            'ef_id' => $request->ef_id,
            'session_type' => $request->session_type,
            'download_time' => Carbon::now(),
        ]);

        // Generate QR code data
        $qr_data = [
            'ticket_id' => $ticket->id,
            'student_id' => $student->id,
            'ef_no' => $ef_list->ef_no,
            'session_type' => $request->session_type,
            'timestamp' => $ticket->download_time->timestamp
        ];

        $qr_code = base64_encode(QrCode::format('png')->size(100)->generate(json_encode($qr_data)));

        return view('tickets.print', compact('ticket', 'student', 'ef_list', 'qr_code'));
    }

    private function checkSessionLimit($ef_list, $session_type)
    {
        $course = $ef_list->course;
        if (!$course) {
            return false;
        }

        // Get total downloaded tickets for this session type
        $downloaded_count = TicketDownload::where('student_id', $ef_list->student_id)
            ->where('ef_id', $ef_list->id)
            ->where('session_type', $session_type)
            ->count();

        $max_sessions = $session_type === 'practical' ?
            $course->practical_sessions :
            $course->theoretical_sessions;

        return $downloaded_count < $max_sessions;
    }

    public function history()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student profile not found.');
        }

        $tickets = TicketDownload::with('efList.course')
            ->where('student_id', $student->id)
            ->orderBy('download_time', 'desc')
            ->paginate(10);

        return view('tickets.history', compact('student', 'tickets'));
    }
}
