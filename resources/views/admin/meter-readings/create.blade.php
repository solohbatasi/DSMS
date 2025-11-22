@extends('layouts.app')

@section('title', 'Record Meter Reading - NYAWASCO')

@section('content')
<!-- Background Image -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 to-white/90"></div>
</div>

<div class="container mx-auto px-4 py-8 relative z-10">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-blue-700">Record Meter Reading</h1>
                <p class="text-gray-600">Record new meter reading for customer</p>
            </div>
            <div class="flex space-x-3">
                <!-- Bill Management Button -->
                <a href="{{ route('bills.index') }}" 
                   class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Bill Management
                </a>
                
                <!-- Customer Profile Button -->
                @if($customer)
                <a href="{{ route('admin.customers.show', $customer) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    {{ $customer->first_name }}'s Profile
                </a>
                @else
                <a href="{{ route('admin.meter-readings.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Readings
                </a>
                @endif
            </div>
        </div>

        <!-- Customer Information Card -->
        @if($customer)
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
            <h2 class="text-xl font-semibold text-blue-700 mb-4">Customer Information</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Customer Name</p>
                    <p class="font-semibold text-gray-900">{{ $customer->first_name }} {{ $customer->last_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Customer ID</p>
                    <p class="font-semibold text-blue-600">{{ $customer->customer_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Location</p>
                    <p class="font-semibold text-gray-900">{{ $customer->plot_number }}, {{ $customer->house_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Meter Number</p>
                    <p class="font-semibold text-green-600">{{ $meter->meter_number ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Reading Information Card -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
            <h2 class="text-xl font-semibold text-green-700 mb-4">Reading Information</h2>
            
            <!-- Previous Reading Info -->
            @if($lastReading)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-blue-800 mb-2">Last Reading</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-blue-600">Reading Date</p>
                        <p class="font-semibold">{{ $lastReading->reading_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-blue-600">Previous Reading</p>
                        <p class="font-semibold">{{ number_format($lastReading->current_reading, 2) }} m³</p>
                    </div>
                </div>
            </div>
            @elseif($meter && $meter->initial_reading)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-yellow-800 mb-2">Initial Meter Reading</h3>
                <p class="text-sm text-yellow-700">This is the first reading for this meter. Initial reading: <strong>{{ number_format($meter->initial_reading, 2) }} m³</strong></p>
            </div>
            @endif

            <!-- Reading Form -->
            <form action="{{ route('admin.meter-readings.store') }}" method="POST" enctype="multipart/form-data" id="readingForm">
                @csrf
                
                <input type="hidden" name="customer_id" value="{{ $customer->id ?? '' }}">

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Current Reading -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Reading (m³) *</label>
                        <input type="number" 
                               name="current_reading" 
                               id="current_reading"
                               step="0.01" 
                               min="{{ $lastReading ? $lastReading->current_reading : ($meter->initial_reading ?? 0) }}"
                               required
                               value="{{ old('current_reading') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200"
                               placeholder="Enter current reading">
                        @error('current_reading')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reading Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reading Date *</label>
                        <input type="date" 
                               name="reading_date" 
                               required
                               max="{{ date('Y-m-d') }}"
                               value="{{ old('reading_date', date('Y-m-d')) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200">
                        @error('reading_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Camera Capture Section -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meter Reading Photo</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-green-400 transition duration-200">
                        <!-- Camera Preview -->
                        <div id="cameraPreview" class="hidden mb-4">
                            <video id="video" width="100%" height="auto" autoplay class="rounded-lg shadow-md"></video>
                            <canvas id="canvas" class="hidden"></canvas>
                        </div>
                        
                        <!-- Captured Image Preview -->
                        <div id="imagePreview" class="hidden mb-4">
                            <img id="preview" src="" alt="Captured reading" class="max-w-full h-auto rounded-lg mx-auto max-h-64 shadow-md">
                            <p class="text-sm text-green-600 mt-2">✓ Photo captured successfully</p>
                        </div>

                        <!-- Action Buttons -->
                        <div id="cameraControls" class="space-y-2">
                            <button type="button" 
                                    id="startCamera" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center mx-auto shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Open Camera
                            </button>
                            
                            <div id="captureControls" class="hidden space-y-2">
                                <button type="button" 
                                        id="capture" 
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center mx-auto shadow-md">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Capture Photo
                                </button>
                                
                                <button type="button" 
                                        id="retake" 
                                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center mx-auto shadow-md">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Retake Photo
                                </button>
                            </div>
                        </div>

                        <!-- File Input Fallback -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-500 mb-2">Or upload existing photo</p>
                            <input type="file" 
                                   name="reading_image" 
                                   id="fileInput"
                                   accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition duration-200">
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea name="notes" 
                              rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200"
                              placeholder="Any additional notes about this reading...">{{ old('notes') }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="mt-8 flex justify-between items-center">
                    <div class="flex space-x-3">
                        <!-- Bill Management Button -->
                        <a href="{{ route('bills.index') }}" 
                           class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center shadow-md">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Bill Management
                        </a>
                        
                        <!-- Customer Profile Button -->
                        @if($customer)
                        <a href="{{ route('admin.customers.show', $customer) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center shadow-md">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ $customer->first_name }}'s Profile
                        </a>
                        @else
                        <a href="{{ route('admin.meter-readings.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Readings
                        </a>
                        @endif
                    </div>
                    
                    <div class="flex space-x-4">
                        @if($customer)
                        <a href="{{ route('admin.customers.show', $customer) }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
                            Cancel
                        </a>
                        @else
                        <a href="{{ route('admin.meter-readings.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
                            Cancel
                        </a>
                        @endif
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg transition duration-200 flex items-center shadow-md">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Record Reading
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include Tesseract.js for OCR -->
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@4/dist/tesseract.min.js"></script>

<!-- Camera and OCR JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const preview = document.getElementById('preview');
    const startCamera = document.getElementById('startCamera');
    const capture = document.getElementById('capture');
    const retake = document.getElementById('retake');
    const cameraPreview = document.getElementById('cameraPreview');
    const imagePreview = document.getElementById('imagePreview');
    const cameraControls = document.getElementById('cameraControls');
    const captureControls = document.getElementById('captureControls');
    const fileInput = document.getElementById('fileInput');
    const currentReadingInput = document.getElementById('current_reading');
    let stream = null;

    // Initialize Tesseract worker
    let tesseractWorker = null;

    // Start Camera
    startCamera.addEventListener('click', async function() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                } 
            });
            video.srcObject = stream;
            cameraPreview.classList.remove('hidden');
            captureControls.classList.remove('hidden');
            startCamera.classList.add('hidden');
        } catch (err) {
            console.error('Error accessing camera:', err);
            alert('Unable to access camera. Please check permissions or use file upload.');
        }
    });

    // Capture Photo and Perform OCR
    capture.addEventListener('click', async function() {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Convert to blob and create file
        canvas.toBlob(function(blob) {
            const file = new File([blob], 'meter-reading-' + Date.now() + '.jpg', { type: 'image/jpeg' });
            
            // Create a new FileList
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
            
            // Show preview
            preview.src = URL.createObjectURL(blob);
            imagePreview.classList.remove('hidden');
            cameraPreview.classList.add('hidden');
            captureControls.classList.add('hidden');
            startCamera.classList.remove('hidden');
            
            // Stop camera
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }

            // Perform OCR on the captured image
            performOCR(canvas);
        }, 'image/jpeg', 0.8);
    });

    // Real OCR Function with Tesseract.js
    async function performOCR(imageElement) {
        try {
            // Show loading state
            Swal.fire({
                title: 'Reading Meter...',
                html: `
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                        <p>Analyzing photo for meter numbers...</p>
                        <p class="text-sm text-gray-500 mt-2">This may take a few seconds</p>
                    </div>
                `,
                allowOutsideClick: false,
                showConfirmButton: false
            });

            // Initialize Tesseract worker if not already done
            if (!tesseractWorker) {
                tesseractWorker = await Tesseract.createWorker();
                await tesseractWorker.loadLanguage('eng');
                await tesseractWorker.initialize('eng');
                
                // Configure for numbers only - better accuracy for meters
                await tesseractWorker.setParameters({
                    tessedit_char_whitelist: '0123456789.',
                    tessedit_pageseg_mode: Tesseract.PSM.SINGLE_BLOCK,
                    tessedit_ocr_engine_mode: Tesseract.OEM.LSTM_ONLY,
                });
            }

            // Perform OCR
            const { data: { text } } = await tesseractWorker.recognize(imageElement);
            
            Swal.close();

            console.log('OCR Raw Text:', text); // For debugging

            // Process the extracted text to find meter reading
            const reading = extractMeterReading(text);
            
            if (reading) {
                showOCRToast(reading, text);
            } else {
                showError('Could not detect valid meter reading. Please enter manually.<br><br>Detected text: "' + (text || 'No text found') + '"');
            }

        } catch (error) {
            console.error('OCR Error:', error);
            Swal.close();
            showError('Error processing image. Please enter reading manually.');
        }
    }

    // Extract meter reading from OCR text
    function extractMeterReading(text) {
        if (!text || text.trim() === '') {
            return null;
        }

        console.log('Processing text:', text); // Debug log

        // Clean the text - remove spaces and non-numeric characters except decimal points
        const cleanText = text.replace(/\s+/g, '').replace(/[^\d.]/g, '');
        
        if (!cleanText) {
            return null;
        }

        console.log('Cleaned text:', cleanText); // Debug log

        // Find all number sequences (including decimals)
        const numberMatches = cleanText.match(/\d+\.?\d*/g);
        
        if (!numberMatches || numberMatches.length === 0) {
            return null;
        }

        console.log('Number matches:', numberMatches); // Debug log

        // Filter reasonable meter readings (typically 1-8 digits, positive numbers)
        const validReadings = numberMatches.filter(num => {
            const value = parseFloat(num);
            // Meter readings are usually positive numbers with reasonable values
            // Adjust these ranges based on your typical meter readings
            return !isNaN(value) && value > 0 && value <= 99999999 && num.length >= 1;
        });

        console.log('Valid readings:', validReadings); // Debug log

        if (validReadings.length === 0) {
            return null;
        }

        // For meter readings, we typically want the largest number
        // (since meters usually show cumulative consumption)
        const largestReading = validReadings.reduce((max, num) => {
            return parseFloat(num) > parseFloat(max) ? num : max;
        });

        const finalReading = parseFloat(largestReading).toFixed(2);
        console.log('Final reading:', finalReading); // Debug log

        return finalReading;
    }

    // OCR Toast Notification
    function showOCRToast(detectedReading, rawText = '') {
        Swal.fire({
            icon: 'info',
            title: 'Reading Detected',
            html: `
                <div class="text-left">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-3">
                        <p class="text-sm font-semibold text-green-800">Detected Reading:</p>
                        <p class="text-2xl font-bold text-green-600">${detectedReading} m³</p>
                    </div>
                    ${rawText ? `
                    <details class="text-xs text-gray-500 mt-2">
                        <summary class="cursor-pointer hover:text-gray-700">Show OCR details</summary>
                        <div class="mt-2 p-2 bg-gray-50 rounded">
                            <strong>Raw text detected:</strong><br>
                            <code class="text-xs">${rawText.substring(0, 100)}${rawText.length > 100 ? '...' : ''}</code>
                        </div>
                    </details>
                    ` : ''}
                    <p class="text-sm text-gray-600 mt-3">Please verify this matches your meter before submitting.</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Use This Reading',
            cancelButtonText: 'Enter Manually',
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6B7280',
            showCloseButton: true,
            width: '500px'
        }).then((result) => {
            if (result.dismiss === 'cancel') {
                // User wants to edit manually - still fill the value but let them edit
                currentReadingInput.value = detectedReading;
                currentReadingInput.focus();
                currentReadingInput.select();
                showInfo('Reading filled. Please verify and edit if needed.');
            } else if (result.isConfirmed) {
                // User confirmed the reading is correct
                currentReadingInput.value = detectedReading;
                showSuccess('✓ Reading confirmed! You can now submit the form.');
            } else {
                // User closed the dialog - still fill the value
                currentReadingInput.value = detectedReading;
            }
        });
    }

    // Retake Photo
    retake.addEventListener('click', function() {
        imagePreview.classList.add('hidden');
        cameraPreview.classList.remove('hidden');
        preview.src = '';
        fileInput.value = '';
        
        // Restart camera
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        startCamera.click();
    });

    // Handle file input change
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                imagePreview.classList.remove('hidden');
                
                // Hide camera controls when file is selected
                cameraPreview.classList.add('hidden');
                captureControls.classList.add('hidden');
                startCamera.classList.remove('hidden');

                // Perform OCR on uploaded file
                const img = new Image();
                img.onload = function() {
                    const tempCanvas = document.createElement('canvas');
                    const tempCtx = tempCanvas.getContext('2d');
                    tempCanvas.width = img.width;
                    tempCanvas.height = img.height;
                    tempCtx.drawImage(img, 0, 0);
                    performOCR(tempCanvas);
                };
                img.src = e.target.result;
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Helper function for success messages
    function showSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: message,
            timer: 3000,
            showConfirmButton: false
        });
    }

    // Helper function for error messages
    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'OCR Failed',
            html: message,
            confirmButtonColor: '#dc2626',
            width: '500px'
        });
    }

    // Helper function for info messages
    function showInfo(message) {
        Swal.fire({
            icon: 'info',
            title: 'Information',
            text: message,
            confirmButtonColor: '#3B82F6',
            timer: 2000,
            showConfirmButton: false
        });
    }

    // Form validation
    document.getElementById('readingForm').addEventListener('submit', function(e) {
        const currentReading = parseFloat(currentReadingInput.value);
        const minReading = parseFloat(currentReadingInput.getAttribute('min'));
        
        if (currentReading < minReading) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Reading',
                text: `Current reading cannot be less than ${minReading.toFixed(2)} m³`,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'OK'
            });
        }
    });

    // Cleanup when leaving page
    window.addEventListener('beforeunload', async () => {
        if (tesseractWorker) {
            await tesseractWorker.terminate();
        }
    });
});
</script>

<!-- SweetAlert2 Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced warning message with custom styling
   
    @if(session('warning'))
        Swal.fire({
            icon: 'warning',
            title: '<span style="color: #d97706">Duplicate Reading Detected</span>',
            html: `<div class="text-left">
                <p class="mb-3">{{ session('warning') }}</p>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-3">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                You can record a new reading in the next billing period.
                            </p>
                        </div>
                    </div>
                </div>
            </div>`,
            confirmButtonColor: '#d97706',
            confirmButtonText: 'Understand',
            showCloseButton: true,
            customClass: {
                popup: 'rounded-lg shadow-xl',
                title: 'text-lg font-semibold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Focus on current reading field after modal closes
                document.querySelector('input[name="current_reading"]')?.focus();
            }
        });
    @endif

    // Error message
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '{{ session('error') }}',
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Try Again',
            showCloseButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector('input[name="current_reading"]')?.focus();
            }
        });
    @endif

    // Success message
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Great!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#059669',
            confirmButtonText: 'Continue',
            timer: 3000,
            timerProgressBar: true,
            showCloseButton: true
        });
    @endif
});
</script>
@endsection