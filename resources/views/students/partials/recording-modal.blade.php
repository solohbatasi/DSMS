<!-- Recording Search Modal -->
<div id="billModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
    <div class="relative bg-white rounded-lg shadow-lg w-full max-w-4xl mx-auto my-8">
        <!-- Header -->
        <div class="flex justify-between items-center border-b px-6 py-4 sticky top-0 bg-white z-10">
            <h3 class="text-lg font-semibold text-gray-800">Record Meter Reading & Generate Bill</h3>
            <button onclick="closeBillModal()" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>

        <!-- Scrollable content -->
        <div class="max-h-[75vh] overflow-y-auto px-6 pb-6">
            <!-- Step 1: Customer Search -->
            <div id="searchStep" class="step active">
                <h4 class="text-md font-semibold text-gray-700 mb-4">Step 1: Find Customer</h4>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Customer</label>
                    <div class="relative">
                        <input type="text" 
                               id="modalCustomerSearch" 
                               placeholder="Start typing name, meter number, customer number, phone..."
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                               autocomplete="off">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        Search by: Customer Name, Meter Number, Customer Number, Phone Number, ID Number
                    </p>
                </div>

                <!-- Live Search Results -->
                <div id="modalSearchResults" class="mt-4 space-y-2 hidden">
                    <h5 class="text-sm font-semibold text-gray-700 mb-3">Matching Customers</h5>
                    <div id="modalResultsList" class="space-y-2 max-h-80 overflow-y-auto border border-gray-200 rounded-lg p-2 bg-gray-50"></div>
                </div>

                <!-- No Results Message -->
                <div id="noResultsMessage" class="hidden mt-4 text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                    <i class="fas fa-search text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-500 font-medium">No customers found</p>
                    <p class="text-gray-400 text-sm mt-1">Try searching with different terms</p>
                </div>

                <!-- Search Tips -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h5 class="text-sm font-semibold text-blue-800 mb-2">Search Tips</h5>
                    <ul class="text-xs text-blue-700 space-y-1">
                        <li>• Start typing to see instant results</li>
                        <li>• Search by customer name, meter number, or phone</li>
                        <li>• Results update as you type</li>
                        <li>• Click on a customer to select</li>
                    </ul>
                </div>
            </div>

            <!-- Step 2: Record Reading -->
            <div id="readingStep" class="step hidden">
                <h4 class="text-md font-semibold text-gray-700 mb-4">Step 2: Record Meter Reading</h4>
                
                <!-- Customer Info Display -->
                <div id="customerInfo" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <!-- Customer details will be populated here -->
                </div>

                <form id="readingForm" action="{{ route('admin.meter-readings.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="customer_id" id="selectedCustomerId">
                    <input type="hidden" name="redirect_to" value="bills_index">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Current Reading -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Reading (m³) *</label>
                            <input type="number" 
                                   name="current_reading" 
                                   id="current_reading"
                                   step="0.01" 
                                   required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   placeholder="Enter current reading">
                        </div>

                        <!-- Reading Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reading Date *</label>
                            <input type="date" 
                                   name="reading_date" 
                                   required
                                   max="{{ date('Y-m-d') }}"
                                   value="{{ date('Y-m-d') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>

                    <!-- Camera Capture Section -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meter Reading Photo (Optional)</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-green-400 transition duration-200">
                            <!-- Camera Preview -->
                            <div id="cameraPreview" class="hidden mb-4">
                                <div class="relative">
                                    <video id="video" width="100%" height="auto" autoplay class="rounded-lg shadow-md"></video>
                                    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                                        <i class="fas fa-camera mr-1"></i> Point camera at meter
                                    </div>
                                </div>
                                <canvas id="canvas" class="hidden"></canvas>
                            </div>
                            
                            <!-- Captured Image Preview -->
                            <div id="imagePreview" class="hidden mb-4">
                                <div class="relative inline-block">
                                    <img id="preview" src="" alt="Captured meter" class="max-w-full h-auto rounded-lg mx-auto max-h-64 shadow-md">
                                    <div id="meterValidation" class="absolute top-2 right-2"></div>
                                </div>
                                <div id="ocrResult" class="mt-3"></div>
                            </div>

                            <!-- Action Buttons -->
                            <div id="cameraControls" class="space-y-2">
                                <button type="button" 
                                        id="startCamera" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center mx-auto shadow-md">
                                    <i class="fas fa-camera mr-2"></i>
                                    Open Camera
                                </button>
                                
                                <div id="captureControls" class="hidden space-y-2">
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-2">
                                        <p class="text-sm text-blue-700 text-left">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            <strong>Tips for better detection:</strong>
                                        </p>
                                        <ul class="text-xs text-blue-600 text-left mt-1 space-y-1">
                                            <li>• Ensure good lighting on the meter</li>
                                            <li>• Capture the entire meter display</li>
                                            <li>• Hold camera steady</li>
                                            <li>• Avoid glare and reflections</li>
                                        </ul>
                                    </div>
                                    
                                    <button type="button" 
                                            id="capture" 
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center mx-auto shadow-md">
                                        <i class="fas fa-camera-retro mr-2"></i>
                                        Capture Meter Photo
                                    </button>
                                    
                                    <button type="button" 
                                            id="retake" 
                                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center mx-auto shadow-md">
                                        <i class="fas fa-redo mr-2"></i>
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
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea name="notes" 
                                  rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                  placeholder="Any additional notes about this reading..."></textarea>
                    </div>

                    <!-- Important Notice -->
                    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Automatic Billing</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Submitting this reading will automatically generate a bill for the customer based on the consumption.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-between items-center border-t px-6 py-4 sticky bottom-0 bg-white">
            <button type="button" onclick="previousStep()" id="prevBtn" class="hidden px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to Search
            </button>
            <div class="flex space-x-2">
                <button type="button" onclick="closeBillModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200">Cancel</button>
                <button type="button" onclick="nextStep()" id="nextBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                    Next<i class="fas fa-arrow-right ml-2"></i>
                </button>
                <button type="button" onclick="submitReading()" id="submitBtn" class="hidden px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                    <i class="fas fa-check mr-2"></i>Record Reading & Generate Bill
                </button>
            </div>
        </div>
    </div>
</div>