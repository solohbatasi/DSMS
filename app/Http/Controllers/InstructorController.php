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
        return view('instructor.qr-verify');
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
                    'message' => 'Ticket not found',
                    'ticket_id' => $ticketId
                ], 404);
            }

            // Check if instructor teaches this course
            $user = Auth::user();
            if ($ticket->efList->course->instructor_id != $user->id) {
                return response()->json([
                    'valid' => false,
                    'message' => 'You are not authorized to verify tickets for this course',
                    'ticket_id' => $ticketId
                ], 403);
            }

            // Check if ticket is for today
            if (!$ticket->download_time->isToday()) {
                return response()->json([
                    'valid' => false,
                    'message' => 'This ticket is not valid for today',
                    'ticket_id' => $ticketId,
                    'student_name' => $ticket->student->name,
                    'course_name' => $ticket->efList->course->course,
                    'download_time' => $ticket->download_time->format('M d, Y h:i A')
                ], 400);
            }

            // Ticket is valid
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
            return response()->json([
                'valid' => false,
                'message' => 'Error verifying ticket: ' . $e->getMessage()
            ], 500);
        }
    }
}
