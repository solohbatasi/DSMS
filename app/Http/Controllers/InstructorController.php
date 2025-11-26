<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TicketDownload;
use App\Models\Student;
use App\Models\Student_ef_list;
use App\Models\Course;

class InstructorController extends Controller
{
    public function showQRVerify()
    {
        // Get recent verifications from session
        $recentVerifications = session('recent_verifications', []);

        return view('instructor.qr-verify', compact('recentVerifications'));
    }

    public function verifyTicket(Request $request)
    {
        $request->validate([
            'ticket_data' => 'required|string'
        ]);

        try {
            // Try to parse as JSON (QR code data)
            $ticketData = json_decode($request->ticket_data, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($ticketData['ticket_id'])) {
                // QR code data format
                $ticketId = $ticketData['ticket_id'];
            } else {
                // Assume it's a direct ticket ID
                $ticketId = $request->ticket_data;
            }

            // Find the ticket
            $ticket = TicketDownload::with(['student', 'efList.course'])
                ->where('id', $ticketId)
                ->first();

            if (!$ticket) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Ticket not found. Please check the ticket ID and try again.',
                    'ticket_id' => $ticketId
                ], 404);
            }

            // Check if ticket is for today
            if (!$ticket->download_time->isToday()) {
                return response()->json([
                    'valid' => false,
                    'message' => 'This ticket is not valid for today. Ticket was downloaded on ' . $ticket->download_time->format('M d, Y'),
                    'ticket_id' => $ticketId,
                    'student_name' => $ticket->student->name,
                    'course_name' => $ticket->efList->course->course,
                    'download_time' => $ticket->download_time->format('M d, Y h:i A')
                ], 400);
            }

            // Ticket is valid - store in recent verifications
            $this->storeRecentVerification([
                'valid' => true,
                'ticket_id' => $ticket->id,
                'student_name' => $ticket->student->name,
                'time' => now()->format('H:i')
            ]);

            return response()->json([
                'valid' => true,
                'ticket_id' => $ticket->id,
                'student_name' => $ticket->student->name,
                'student_id' => $ticket->student->id_no,
                'course_name' => $ticket->efList->course->course,
                'session_type' => $ticket->session_type,
                'download_time' => $ticket->download_time->format('M d, Y h:i A'),
                'ef_no' => $ticket->efList->ef_no
            ]);

        } catch (\Exception $e) {
            \Log::error('QR Verification Error: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'message' => 'An error occurred while verifying the ticket. Please try again.'
            ], 500);
        }
    }

    public function verifyTicketManual(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|string|max:255'
        ]);

        try {
            $ticketId = $request->ticket_id;

            // Find the ticket
            $ticket = TicketDownload::with(['student', 'efList.course'])
                ->where('id', $ticketId)
                ->first();

            if (!$ticket) {
                return back()->with('error', 'Ticket not found. Please check the ticket ID and try again.');
            }

            // Check if ticket is for today
            if (!$ticket->download_time->isToday()) {
                return back()->with('error', 'This ticket is not valid for today. Ticket was downloaded on ' . $ticket->download_time->format('M d, Y'));
            }

            // Store successful verification
            $this->storeRecentVerification([
                'valid' => true,
                'ticket_id' => $ticket->id,
                'student_name' => $ticket->student->name,
                'time' => now()->format('H:i')
            ]);

            return back()->with('success', "Ticket verified successfully! Student: {$ticket->student->name}, Course: {$ticket->efList->course->course}");

        } catch (\Exception $e) {
            \Log::error('Manual Ticket Verification Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while verifying the ticket. Please try again.');
        }
    }

    private function storeRecentVerification($verification)
    {
        $recentVerifications = session('recent_verifications', []);

        // Add new verification to beginning of array
        array_unshift($recentVerifications, $verification);

        // Keep only last 5 verifications
        $recentVerifications = array_slice($recentVerifications, 0, 5);

        session(['recent_verifications' => $recentVerifications]);
    }
}
