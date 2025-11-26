@extends('layouts.app')
@props(['title' => 'QR Code Verification', 'subtitle' => 'Verify student session tickets'])

@section('content')
<div class="min-h-screen bg-gray-50">
    @include('components.dashboard-header')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">QR Code Verification</h1>
                    <p class="text-gray-600 mt-2">Scan student tickets to verify session attendance</p>
                </div>
                <div class="bg-indigo-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-indigo-800">Allow camera access when prompted</span>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-green-800 font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-red-800 font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- QR Scanner Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Scan QR Code</h3>

                <!-- Camera Permission Alert -->
                <div id="camera-permission-alert" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm text-yellow-800" id="permission-message"></span>
                    </div>
                </div>

                <!-- QR Scanner Container -->
                <div id="qr-scanner" class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center mb-4">
                    <div id="reader" class="w-full mx-auto" style="max-width: 300px;"></div>
                    <div id="scanner-status" class="mt-4">
                        <p class="text-sm text-gray-500">Position QR code within the frame to scan</p>
                    </div>
                </div>

                <!-- Scanner Controls -->
                <div class="flex justify-center space-x-4 mb-4">
                    <button id="start-scanner" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-200">
                        Start Scanner
                    </button>
                    <button id="stop-scanner" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200 hidden">
                        Stop Scanner
                    </button>
                </div>

                <!-- Manual Input Fallback -->
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Manual Ticket Verification</h4>
                    <form method="POST" action="{{ route('instructor.verify-ticket-manual') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label for="ticket_id" class="block text-sm font-medium text-gray-700 mb-1">Ticket ID</label>
                            <input type="text" id="ticket_id" name="ticket_id"
                                   placeholder="Enter Ticket ID"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-200">
                            Verify Ticket
                        </button>
                    </form>
                </div>
            </div>

            <!-- Verification Results -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Verification Results</h3>

                <div id="verification-result">
                    <div class="text-center py-8">
                        <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-600">Scan a QR code to verify ticket</p>
                    </div>
                </div>

                <!-- Recent Verifications -->
                <div class="border-t border-gray-200 pt-4 mt-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Recent Verifications</h4>
                    <div id="recent-verifications" class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($recentVerifications ?? [] as $verification)
                        <div class="flex items-center justify-between p-2 rounded-lg {{ $verification['valid'] ? 'bg-green-50' : 'bg-red-50' }}">
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center {{ $verification['valid'] ? 'bg-green-100' : 'bg-red-100' }}">
                                    <svg class="w-3 h-3 {{ $verification['valid'] ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($verification['valid'])
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        @endif
                                    </svg>
                                </div>
                                <span class="text-sm font-medium">{{ $verification['student_name'] ?? 'Unknown' }}</span>
                            </div>
                            <span class="text-xs text-gray-500">{{ $verification['time'] }}</span>
                        </div>
                        @endforeach
                        @empty($recentVerifications)
                        <p class="text-sm text-gray-500 text-center py-4">No recent verifications</p>
                        @endempty
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include HTML5 QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>

<script>
class QRVerifier {
    constructor() {
        this.html5QrcodeScanner = null;
        this.isScanning = false;
        this.setupEventListeners();
    }

    setupEventListeners() {
        document.getElementById('start-scanner').addEventListener('click', () => {
            this.startScanner();
        });

        document.getElementById('stop-scanner').addEventListener('click', () => {
            this.stopScanner();
        });
    }

    async startScanner() {
        try {
            // Show loading state
            this.updateScannerStatus('Initializing camera...', 'text-blue-600');

            // Check if scanner already exists
            if (this.html5QrcodeScanner) {
                await this.html5QrcodeScanner.clear();
            }

            // Create new scanner
            this.html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_QR_CODE]
                },
                false
            );

            // Start scanning
            await this.html5QrcodeScanner.render(
                (decodedText, decodedResult) => {
                    this.onScanSuccess(decodedText, decodedResult);
                },
                (errorMessage) => {
                    // Ignore scan failures unless it's a permission error
                    if (errorMessage.includes('Permission')) {
                        this.showCameraError('Camera permission denied. Please allow camera access and try again.');
                    }
                }
            );

            this.isScanning = true;
            this.updateScannerStatus('Scanner active - Point camera at QR code', 'text-green-600');
            this.toggleScannerButtons(true);

        } catch (error) {
            console.error('Scanner error:', error);
            this.handleScannerError(error);
        }
    }

    async stopScanner() {
        if (this.html5QrcodeScanner && this.isScanning) {
            try {
                await this.html5QrcodeScanner.clear();
                this.isScanning = false;
                this.updateScannerStatus('Scanner stopped', 'text-gray-600');
                this.toggleScannerButtons(false);
                document.getElementById('reader').innerHTML = '';
            } catch (error) {
                console.error('Error stopping scanner:', error);
            }
        }
    }

    onScanSuccess(decodedText, decodedResult) {
        // Stop scanner temporarily
        this.stopScanner();

        // Show processing message
        this.updateScannerStatus('Processing ticket...', 'text-blue-600');

        // Verify the ticket
        this.verifyTicket(decodedText);

        // Restart scanner after a delay
        setTimeout(() => {
            this.startScanner();
        }, 3000);
    }

    async verifyTicket(ticketData) {
        try {
            this.showLoading();

            const response = await fetch('{{ route("instructor.verify-ticket") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ticket_data: ticketData })
            });

            const result = await response.json();

            if (response.ok) {
                this.showVerificationResult(result);
                this.addToRecentVerifications(result);
            } else {
                this.showError(result.message || 'Verification failed');
            }
        } catch (error) {
            console.error('Verification error:', error);
            this.showError('Network error. Please check your connection and try again.');
        }
    }

    showLoading() {
        const resultDiv = document.getElementById('verification-result');
        resultDiv.innerHTML = `
            <div class="text-center py-8">
                <div class="h-16 w-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-blue-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <p class="text-gray-600">Verifying ticket...</p>
            </div>
        `;
    }

    showVerificationResult(result) {
        const resultDiv = document.getElementById('verification-result');
        const isSuccess = result.valid;

        resultDiv.innerHTML = `
            <div class="border rounded-lg p-4 ${isSuccess ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'}">
                <div class="flex items-center mb-3">
                    <div class="h-8 w-8 rounded-full flex items-center justify-center ${isSuccess ? 'bg-green-100' : 'bg-red-100'}">
                        <svg class="h-5 w-5 ${isSuccess ? 'text-green-600' : 'text-red-600'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${isSuccess ?
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
                            }
                        </svg>
                    </div>
                    <h4 class="ml-2 text-lg font-semibold ${isSuccess ? 'text-green-800' : 'text-red-800'}">
                        ${isSuccess ? 'Ticket Verified' : 'Invalid Ticket'}
                    </h4>
                </div>
                ${isSuccess ? this.renderSuccessDetails(result) : this.renderErrorDetails(result)}
            </div>
        `;
    }

    renderSuccessDetails(result) {
        return `
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Student:</span>
                    <span class="font-medium">${result.student_name}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Student ID:</span>
                    <span class="font-medium">${result.student_id}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Course:</span>
                    <span class="font-medium">${result.course_name}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">EF No:</span>
                    <span class="font-medium">${result.ef_no}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Session Type:</span>
                    <span class="font-medium capitalize">${result.session_type}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Download Time:</span>
                    <span class="font-medium">${result.download_time}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ticket ID:</span>
                    <span class="font-mono text-xs">${result.ticket_id}</span>
                </div>
            </div>
            <div class="mt-4 p-3 bg-green-100 rounded-lg">
                <p class="text-sm text-green-800 font-medium">✓ Student is authorized for this session</p>
            </div>
        `;
    }

    renderErrorDetails(result) {
        return `
            <div class="space-y-2 text-sm">
                <p class="text-red-700">${result.message}</p>
                ${result.ticket_id ? `
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ticket ID:</span>
                        <span class="font-mono text-xs">${result.ticket_id}</span>
                    </div>
                ` : ''}
            </div>
        `;
    }

    showError(message) {
        const resultDiv = document.getElementById('verification-result');
        resultDiv.innerHTML = `
            <div class="border border-red-200 bg-red-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h4 class="ml-2 text-lg font-semibold text-red-800">Error</h4>
                </div>
                <p class="mt-2 text-sm text-red-700">${message}</p>
            </div>
        `;
    }

    showCameraError(message) {
        const alertDiv = document.getElementById('camera-permission-alert');
        const messageDiv = document.getElementById('permission-message');
        messageDiv.textContent = message;
        alertDiv.classList.remove('hidden');
        this.updateScannerStatus('Camera access required', 'text-red-600');
    }

    handleScannerError(error) {
        console.error('Scanner initialization error:', error);

        if (error.name === 'NotAllowedError') {
            this.showCameraError('Camera permission denied. Please allow camera access in your browser settings.');
        } else if (error.name === 'NotFoundError') {
            this.showCameraError('No camera found. Please check if your device has a camera.');
        } else if (error.name === 'NotSupportedError') {
            this.showCameraError('Camera not supported in this browser. Try using Chrome or Firefox.');
        } else {
            this.showCameraError('Failed to initialize camera. Please try again.');
        }
    }

    updateScannerStatus(message, textColor = 'text-gray-600') {
        const statusDiv = document.getElementById('scanner-status');
        statusDiv.innerHTML = `<p class="text-sm ${textColor}">${message}</p>`;
    }

    toggleScannerButtons(scanning) {
        document.getElementById('start-scanner').classList.toggle('hidden', scanning);
        document.getElementById('stop-scanner').classList.toggle('hidden', !scanning);
    }

    addToRecentVerifications(result) {
        const recentDiv = document.getElementById('recent-verifications');

        // Remove "no verifications" message if it exists
        const noVerificationsMsg = recentDiv.querySelector('p');
        if (noVerificationsMsg) {
            noVerificationsMsg.remove();
        }

        const verificationItem = document.createElement('div');
        verificationItem.className = `flex items-center justify-between p-2 rounded-lg ${result.valid ? 'bg-green-50' : 'bg-red-50'}`;

        verificationItem.innerHTML = `
            <div class="flex items-center space-x-2">
                <div class="w-6 h-6 rounded-full flex items-center justify-center ${result.valid ? 'bg-green-100' : 'bg-red-100'}">
                    <svg class="w-3 h-3 ${result.valid ? 'text-green-600' : 'text-red-600'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${result.valid ?
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
                        }
                    </svg>
                </div>
                <span class="text-sm font-medium">${result.student_name || 'Unknown'}</span>
            </div>
            <span class="text-xs text-gray-500">${new Date().toLocaleTimeString()}</span>
        `;

        recentDiv.insertBefore(verificationItem, recentDiv.firstChild);

        // Limit to 5 recent items
        if (recentDiv.children.length > 5) {
            recentDiv.removeChild(recentDiv.lastChild);
        }
    }
}

// Initialize QR Verifier when page loads
document.addEventListener('DOMContentLoaded', function() {
    new QRVerifier();

    // Auto-start scanner on page load (optional)
    // setTimeout(() => {
    //     document.getElementById('start-scanner').click();
    // }, 1000);
});
</script>

<style>
#reader {
    min-height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
}

#reader video {
    border-radius: 8px;
    max-width: 100%;
}

#reader__dashboard_section_swaplink {
    display: none !important;
}
</style>
@endsection
