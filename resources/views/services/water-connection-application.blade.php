@extends('layouts.app')

@section('content')
<section class="relative bg-gradient-to-b from-sky-50 to-white min-h-screen flex items-center justify-center py-16 overflow-hidden">

    <!-- Decorative water elements -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none">
        <img src="https://wallpapercave.com/wp/wp11123245.jpg" class="w-full h-full opacity-70 object-cover" alt="water pattern">
    </div>

    <div class="w-full max-w-4xl mx-auto px-4 md:px-8 relative z-10">

        <!-- Header with Logo -->
        <div class="flex flex-col md:flex-row items-center justify-between mb-10 md:mb-12 text-center bg-gradient-to-r from-sky-400 to-blue-500 rounded-3xl md:text-left">
            <div class="flex items-center justify-center md:justify-start space-x-4 mb-4 md:mb-0">
                <img src="{{ asset('img/Logo.png') }}" alt="NYEWASCO Logo" class="h-16 md:h-20">
                <h1 class="text-2xl md:text-3xl font-bold text-sky-700 leading-snug">
                    Water Connection Application Form
                </h1>
            </div>
            <p class="text-gray-900 max-w-md mx-auto md:mx-0">
                Fill in the form below to apply for a new water connection. Ensure all required documents are attached.
            </p>
        </div>

        <!-- Success Modal -->
        @if(session('success'))
        <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-auto transform transition-all duration-300 scale-95 hover:scale-100">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-6 rounded-t-2xl text-center">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Application Submitted!</h3>
                    <p class="text-green-100 text-lg">{{ session('success') }}</p>
                </div>

                <!-- Modal Body -->
                <div class="p-6 text-center">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-600 mb-2">Your application has been received successfully.</p>
                    <p class="text-gray-500 text-sm mb-6">Reference ID: #WC{{ \App\Models\WaterConnectionApplication::latest()->first()->id ?? '0000' }}</p>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-4 rounded-b-2xl flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('water-connection.apply') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200 flex-1 text-center font-semibold flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Another Application
                    </a>
                    <a href="{{ url('/') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition duration-200 flex-1 text-center font-semibold flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Go Home
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- File Size Error Modal -->
        <div id="fileErrorModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 hidden">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-auto transform transition-all duration-300">
                <!-- Modal Header -->
                <div class="bg-red-500 p-6 rounded-t-2xl text-center">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">File Too Large</h3>
                </div>

                <!-- Modal Body -->
                <div class="p-6 text-center">
                    <p class="text-gray-700 mb-4" id="fileErrorText"></p>
                    <div class="text-sm text-gray-600 mb-4">
                        <p>Please compress your files or use smaller files:</p>
                        <ul class="text-left mt-2 space-y-1">
                            <li>• Use PDF format for documents</li>
                            <li>• Compress images before uploading</li>
                            <li>• Ensure files meet the size requirements</li>
                        </ul>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-4 rounded-b-2xl text-center">
                    <button onclick="closeFileErrorModal()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition duration-200 font-semibold">
                        OK, I Understand
                    </button>
                </div>
            </div>
        </div>

        <form id="waterConnectionForm" action="{{ route('water-connection.submit') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 md:p-10 rounded-3xl shadow-xl mx-auto space-y-10">
            @csrf

            <!-- PART A: Applicant Details -->
            <h2 class="text-xl md:text-2xl font-semibold text-sky-700 mb-6 border-b border-sky-300 pb-2 text-center md:text-left">PART A: Applicant / Customer Details</h2>
            <div class="grid md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block mb-1 font-medium text-gray-700">First Name</label>
                    <input type="text" name="first_name" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Last Name</label>
                    <input type="text" name="last_name" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Phone Number</label>
                    <input type="text" name="phone" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Gender</label>
                    <select name="gender" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:outline-none" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">KRA Pin Number</label>
                    <input type="text" name="kra_pin" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">
                        Attach KRA Pin <span class="text-xs text-red-500">(Required)</span>
                        <span class="text-xs text-gray-500 block">Max: 1MB • PDF, JPG, JPEG, PNG</span>
                    </label>
                    <input type="file" name="kra_pin_file" 
                           class="w-full border border-gray-300 rounded px-3 py-2 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                           accept=".pdf,.jpg,.jpeg,.png"
                           data-max-size="1048576">
                    <div class="file-feedback text-xs mt-1 hidden"></div>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">National ID Number</label>
                    <input type="text" name="national_id" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">
                        Attach National ID <span class="text-xs text-red-500">(Required)</span>
                        <span class="text-xs text-gray-500 block">Max: 1MB • PDF, JPG, JPEG, PNG</span>
                    </label>
                    <input type="file" name="national_id_file" 
                           class="w-full border border-gray-300 rounded px-3 py-2 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                           accept=".pdf,.jpg,.jpeg,.png"
                           data-max-size="1048576">
                    <div class="file-feedback text-xs mt-1 hidden"></div>
                </div>
            </div>

            <!-- PART B: Water Connection Details -->
            <h2 class="text-xl md:text-2xl font-semibold text-sky-700 mb-6 border-b border-sky-300 pb-2 text-center md:text-left">PART B: Details of Water Connection</h2>
            <div class="grid md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Plot Number</label>
                    <input type="text" name="plot_number" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">House Number</label>
                    <input type="text" name="house_number" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Estate</label>
                    <input type="text" name="estate" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:outline-none">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Expected Number of Users</label>
                    <input type="number" name="expected_users" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:outline-none">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Name of Property Owner</label>
                    <input type="text" name="property_owner" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">
                        Attach Title Document <span class="text-xs text-gray-500">(Optional)</span>
                        <span class="text-xs text-gray-500 block">Max: 2MB • PDF, JPG, JPEG, PNG</span>
                    </label>
                    <input type="file" name="title_document" 
                           class="w-full border border-gray-300 rounded px-3 py-2 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                           accept=".pdf,.jpg,.jpeg,.png"
                           data-max-size="2097152">
                    <div class="file-feedback text-xs mt-1 hidden"></div>
                </div>
            </div>

            <!-- PART C: General Conditions -->
            <h2 class="text-xl md:text-2xl font-semibold text-sky-700 mb-4 border-b border-sky-300 pb-2 text-center md:text-left">PART C: General Conditions</h2>
            <p class="text-gray-700 mb-6 text-sm md:text-base">
                I hereby apply for a water and/or sewerage connection to the above premises. I understand that the connection will be effected after the Company's approval and payment of all fees and charges due and owing. I agree to pay for the services in accordance to the prevailing tariffs and subsequent revisions as gazetted in law. The Company shall not accept personal cheques as mode of payment. I undertake to abide to the by-laws and regulations relating to water supply and sewerage services.
            </p>

            <div class="grid md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Signature</label>
                    <input type="text" name="signature" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:outline-none">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Date</label>
                    <input type="date" name="date" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-sky-400 focus:outline-none" required>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-8">
                <button type="submit" id="submitButton" class="px-8 py-3 bg-gradient-to-r from-sky-700 to-blue-600 text-white font-semibold rounded-full shadow-lg hover:from-sky-800 hover:to-blue-700 transition transform hover:-translate-y-1 duration-300">
                    Submit Application
                </button>
            </div>

        </form>
    </div>
</section>

<!-- Enhanced JavaScript for File Validation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('waterConnectionForm');
    const fileInputs = document.querySelectorAll('input[type="file"]');
    const submitButton = document.getElementById('submitButton');
    
    // File size limits in bytes
    const sizeLimits = {
        'kra_pin_file': 1048576, // 1MB
        'national_id_file': 1048576, // 1MB
        'title_document': 2097152 // 2MB
    };
    
    // File type validation
    const allowedTypes = [
        'application/pdf',
        'image/jpeg',
        'image/jpg',
        'image/png'
    ];
    
    // Format file size for display
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Show file feedback
    function showFileFeedback(input, message, isError = false) {
        const feedbackDiv = input.parentElement.querySelector('.file-feedback');
        feedbackDiv.textContent = message;
        feedbackDiv.classList.remove('hidden');
        
        if (isError) {
            feedbackDiv.classList.add('text-red-500');
            feedbackDiv.classList.remove('text-green-600', 'text-blue-600');
            input.classList.add('border-red-500', 'border-2');
            input.classList.remove('border-green-500');
        } else {
            feedbackDiv.classList.add('text-green-600');
            feedbackDiv.classList.remove('text-red-500', 'text-blue-600');
            input.classList.add('border-green-500', 'border-2');
            input.classList.remove('border-red-500');
        }
    }
    
    // Clear file feedback
    function clearFileFeedback(input) {
        const feedbackDiv = input.parentElement.querySelector('.file-feedback');
        feedbackDiv.textContent = '';
        feedbackDiv.classList.add('hidden');
        input.classList.remove('border-red-500', 'border-green-500', 'border-2');
    }
    
    // Show loading feedback
    function showFileLoading(input) {
        const feedbackDiv = input.parentElement.querySelector('.file-feedback');
        feedbackDiv.textContent = 'Checking file...';
        feedbackDiv.classList.remove('hidden');
        feedbackDiv.classList.add('text-blue-600');
        feedbackDiv.classList.remove('text-red-500', 'text-green-600');
    }
    
    // Validate individual file
    function validateFile(input) {
        const file = input.files[0];
        if (!file) {
            clearFileFeedback(input);
            return true;
        }
        
        showFileLoading(input);
        
        const maxSize = sizeLimits[input.name];
        const fileSize = file.size;
        const fileName = file.name;
        
        // Simulate processing delay for better UX
        setTimeout(() => {
            // Check file size
            if (fileSize > maxSize) {
                const maxSizeFormatted = formatFileSize(maxSize);
                const fileSizeFormatted = formatFileSize(fileSize);
                showFileFeedback(input, `❌ File too large: ${fileSizeFormatted}. Maximum allowed: ${maxSizeFormatted}`, true);
                return false;
            }
            
            // Check file type
            if (!allowedTypes.includes(file.type)) {
                showFileFeedback(input, '❌ Invalid file type. Please upload PDF, JPG, or PNG files only.', true);
                return false;
            }
            
            // File is valid
            const fileSizeFormatted = formatFileSize(fileSize);
            showFileFeedback(input, `✅ ${fileName} (${fileSizeFormatted})`, false);
            return true;
        }, 500);
        
        return true; // Return true initially, validation happens async
    }
    
    // Validate all files synchronously
    function validateAllFilesSync() {
        let allValid = true;
        let errorFiles = [];
        
        fileInputs.forEach(input => {
            const file = input.files[0];
            if (!file) {
                // Optional files can be empty, required files should be checked separately
                if (input.name !== 'title_document') {
                    // KRA Pin and National ID are required
                    const feedbackDiv = input.parentElement.querySelector('.file-feedback');
                    feedbackDiv.textContent = '⚠️ This document is required';
                    feedbackDiv.classList.remove('hidden');
                    feedbackDiv.classList.add('text-yellow-600');
                    allValid = false;
                }
                return;
            }
            
            const maxSize = sizeLimits[input.name];
            const fileSize = file.size;
            
            if (fileSize > maxSize) {
                allValid = false;
                const maxSizeFormatted = formatFileSize(maxSize);
                const fileSizeFormatted = formatFileSize(fileSize);
                errorFiles.push(`${input.name.replace(/_/g, ' ')}: ${fileSizeFormatted} (max: ${maxSizeFormatted})`);
            }
            
            if (!allowedTypes.includes(file.type)) {
                allValid = false;
                errorFiles.push(`${input.name.replace(/_/g, ' ')}: Invalid file type`);
            }
        });
        
        return { allValid, errorFiles };
    }
    
    // Show file error modal
    function showFileErrorModal(message) {
        document.getElementById('fileErrorText').textContent = message;
        document.getElementById('fileErrorModal').classList.remove('hidden');
    }
    
    // Close file error modal
    function closeFileErrorModal() {
        document.getElementById('fileErrorModal').classList.add('hidden');
    }
    
    // Real-time file validation on change
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            validateFile(this);
        });
    });
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate files synchronously
        const validation = validateAllFilesSync();
        
        if (!validation.allValid) {
            let errorMessage = 'Please fix the following file issues:\n\n';
            validation.errorFiles.forEach(error => {
                errorMessage += `• ${error}\n`;
            });
            errorMessage += '\nPlease compress your files or choose smaller files.';
            
            showFileErrorModal(errorMessage);
            return;
        }
        
        // Check required files
        const kraPinFile = document.querySelector('input[name="kra_pin_file"]').files[0];
        const nationalIdFile = document.querySelector('input[name="national_id_file"]').files[0];
        
        if (!kraPinFile || !nationalIdFile) {
            showFileErrorModal('Please attach both KRA Pin and National ID documents. These are required for application processing.');
            return;
        }
        
        // Check total form data size (approximate)
        const formData = new FormData(form);
        let totalSize = 0;
        
        for (let [key, value] of formData.entries()) {
            if (value instanceof File) {
                totalSize += value.size;
            } else {
                totalSize += new Blob([value]).size;
            }
        }
        
        // Warn if total size is very large (approx 5MB+)
        if (totalSize > 5 * 1024 * 1024) {
            if (!confirm('The total size of your submission is quite large. This may take a while to upload. Do you want to continue?')) {
                return;
            }
        }
        
        // If all valid, submit the form
        submitButton.disabled = true;
        submitButton.innerHTML = 'Submitting... <svg class="w-4 h-4 ml-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 000-20z"></path></svg>';
        
        // Submit the form
        this.submit();
    });
    
    // Add some visual feedback for file inputs
    fileInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('ring-2', 'ring-blue-200', 'rounded-lg', 'p-1');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('ring-2', 'ring-blue-200', 'rounded-lg', 'p-1');
        });
        
        input.addEventListener('dragenter', function() {
            this.parentElement.classList.add('ring-2', 'ring-yellow-300', 'bg-yellow-50', 'rounded-lg');
        });
        
        input.addEventListener('dragleave', function() {
            this.parentElement.classList.remove('ring-2', 'ring-yellow-300', 'bg-yellow-50', 'rounded-lg');
        });
    });
});

// File error modal functions
function closeFileErrorModal() {
    document.getElementById('fileErrorModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('fileErrorModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeFileErrorModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFileErrorModal();
    }
});

// Success modal functions (your existing ones)
document.addEventListener('DOMContentLoaded', function() {
    const successModal = document.getElementById('successModal');
    
    if (successModal) {
        // Show modal with animation
        setTimeout(() => {
            successModal.classList.add('flex');
            successModal.classList.remove('hidden');
        }, 100);

        // Close modal when clicking outside
        successModal.addEventListener('click', function(e) {
            if (e.target === successModal) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && successModal) {
                closeModal();
            }
        });
    }

    function closeModal() {
        if (successModal) {
            successModal.classList.add('hidden');
            successModal.classList.remove('flex');
        }
    }

    // Add some animation to form elements
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('ring-2', 'ring-sky-200', 'rounded-lg');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('ring-2', 'ring-sky-200', 'rounded-lg');
        });
    });
});
</script>

<style>
/* Custom animations */
@keyframes modalEnter {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

#successModal > div,
#fileErrorModal > div {
    animation: modalEnter 0.3s ease-out;
}

/* Smooth transitions */
.transition-all {
    transition: all 0.3s ease-in-out;
}

/* Form styling enhancements */
input:focus, select:focus, textarea:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(56, 189, 248, 0.1);
}

/* File input styling */
input[type="file"]:valid {
    border-color: #10b981;
}

input[type="file"]:invalid {
    border-color: #ef4444;
}

/* Loading animation */
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* File upload zone styling */
.file-upload-zone {
    border: 2px dashed #d1d5db;
    border-radius: 0.5rem;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
}

.file-upload-zone.drag-over {
    border-color: #3b82f6;
    background-color: #eff6ff;
}

.file-upload-zone.has-file {
    border-color: #10b981;
    background-color: #ecfdf5;
}
</style>
@endsection