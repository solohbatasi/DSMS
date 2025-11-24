<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Ticket - {{ $student->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: 80mm 150mm;
                margin: 0;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }

        body {
            font-family: 'Courier New', monospace;
            width: 80mm;
            margin: 0 auto;
            background: white;
        }

        .ticket {
            padding: 8px;
            border: 2px dashed #000;
            background: white;
        }

        .redownload-notice {
            background: #fff3cd;
            border: 1px dashed #ffc107;
            padding: 4px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Print Controls -->
    <div class="no-print fixed top-4 right-4 z-50">
        <div class="bg-white rounded-lg shadow-lg p-4 space-y-2">
            <button onclick="window.print()" class="w-full bg-blue-600 text-white px-4 py-2 rounded text-sm font-medium hover:bg-blue-700">
                 Print Ticket
            </button>
            <button onclick="window.close()" class="w-full bg-gray-600 text-white px-4 py-2 rounded text-sm font-medium hover:bg-gray-700">
                 Close
            </button>
            <a href="{{ route('student.tickets') }}" class="block w-full bg-green-600 text-white px-4 py-2 rounded text-sm font-medium hover:bg-green-700 text-center no-underline">
                 New Ticket
            </a>
        </div>
    </div>

    <!-- Thermal Ticket -->
    <div class="ticket">
        <!-- Header -->
        <div class="text-center border-b-2 border-black pb-2 mb-2">
            <h1 class="text-xl font-bold uppercase"> {{ config('app.name', 'PACE DRIVING SCH') }}</h1>
            @if(isset($qr_data) && (strpos($qr_data, '"redownloaded":true') !== false))
                <div class="redownload-notice mt-1">
                    REDOWNLOADED TICKET
                </div>
            @endif
            @if(isset($qr_data) && (strpos($qr_data, '"preview":true') !== false))
                <div class="bg-blue-100 border border-blue-300 p-1 mt-1 text-xs">
                    PREVIEW MODE
                </div>
            @endif

        </div>

        <!-- Student Info -->
        <div class="space-y-1 text-sm mb-3">
            <div class="flex justify-between">
                <span class="font-medium">Name:</span>
                <span>{{ strtoupper($student->name) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium">ID No:</span>
                <span>{{ $student->id_no }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium">EF No:</span>
                <span>{{ $ef_list->ef_no }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium">Ticket ID:</span>
                <span class="text-xs">{{ $ticket->id }}</span>
            </div>
        </div>

        <!-- Course Info -->
        <div class="border-t border-dashed border-black pt-2 mb-3">
            <div class="text-center font-bold text-sm mb-1">
                {{ $course->course }}
            </div>
        </div>

        <!-- Session Details -->
        <div class="bg-gray-200 p-2 text-center mb-3">
            <div class="text-lg font-bold uppercase">
                {{ $session_type }} SESSION
            </div>
            <div class="text-xs">
                Date: {{ $ticket->download_time->format('M d, Y') }}
            </div>
            <div class="text-xs">
                Time: {{ $ticket->download_time->format('h:i A') }}
            </div>
            @if($ticket->download_time->isToday())
                <div class="text-xs text-green-600 font-bold mt-1">
                    VALID FOR TODAY
                </div>
            @endif
        </div>
        <!-- Attendance Summary -->
        <div class="border-t border-dashed border-black pt-2 mb-3">
            <div class="text-center text-sm font-bold mb-1">ATTENDANCE SUMMARY</div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="text-center">
                    <div>Practical</div>
                    <div class="font-bold">{{ $ef_list->attended_practical }}/{{ $ef_list->attended_practical + $ef_list->remaining_practical }}</div>
                </div>
                <div class="text-center">
                    <div>Theoretical</div>
                    <div class="font-bold">{{ $ef_list->attended_theoretical }}/{{ $ef_list->attended_theoretical + $ef_list->remaining_theoretical }}</div>
                </div>
            </div>
        </div>
        <!-- QR Code -->
        <div class="text-center mb-3">
            @if(isset($qr_code_base64))
                <img src="{{ $qr_code_base64 }}" alt="QR Code" class="inline-block mx-auto" style="width: 120px; height: 120px;">
            @else
                <div class="inline-block bg-gray-200 w-32 h-32 flex items-center justify-center text-xs text-gray-500">
                    QR Code<br>Not Available
                </div>
            @endif
            <div class="text-xs mt-1">Scan to verify</div>
        </div>

        <!-- Footer -->
        <div class="border-t border-dashed border-black pt-2 text-xs text-center">

            <div class="mt-1 text-[10px]">
                Generated: {{ $ticket->download_time->format('Y-m-d H:i:s') }}
                @if(isset($qr_data) && (strpos($qr_data, '"redownloaded":true') !== false))
                    <br>Redownloaded: {{ now()->format('Y-m-d H:i:s') }}
                @endif
            </div>
        </div>
    </div>

    <script>
        // Auto-print if needed
        @if(request()->has('autoprint'))
        window.onload = function() {
            window.print();
        }
        @endif
    </script>
</body>
</html>
