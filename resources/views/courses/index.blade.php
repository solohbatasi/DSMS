@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
@endphp

@can('view courses')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    @php
    $actionButtons = [];

    if (auth()->user()->can('add courses')) {
        $actionButtons[] = [
            'text' => 'Add Course',
            'onclick' => 'openCourseModal()',
            'icon' => 'fas fa-plus',
            'color' => 'bg-green-600 hover:bg-green-700'
        ];
    }
    @endphp

    @include('components.dashboard-header', [
        'title' => 'Courses Management',
        'subtitle' => 'Manage all driving courses and programs',
        'actionButtons' => $actionButtons
    ])

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">

            <!-- Total Courses -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Courses</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalCourses }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Courses -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Active Courses</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $activeCourses }}</p>
                    </div>
                </div>
            </div>

            <!-- Inactive Courses -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-pause-circle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Inactive Courses</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $inactiveCourses }}</p>
                    </div>
                </div>
            </div>

            <!-- Popular Courses -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-star text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Popular Courses</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $popularCourses->count() }}</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Quick Actions -->
        @can('add courses')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                <!-- All Courses -->
                <a href="{{ route('courses.index') }}"
                class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center hover:bg-blue-100 transition duration-200 group">
                    <i class="fas fa-list text-blue-600 text-xl mb-2 group-hover:text-blue-700"></i>
                    <p class="text-sm font-medium text-blue-700">All Courses</p>
                    <p class="text-xs text-blue-600 mt-1">View all courses</p>
                </a>

                <!-- Add Course -->
                <a href="javascript:void(0)"
                onclick="openCourseModal()"
                class="bg-green-50 border border-green-200 rounded-lg p-4 text-center hover:bg-green-100 transition duration-200 group">
                    <i class="fas fa-plus text-green-600 text-xl mb-2 group-hover:text-green-700"></i>
                    <p class="text-sm font-medium text-green-700">Add Course</p>
                    <p class="text-xs text-green-600 mt-1">Create new course</p>
                </a>

                <!-- Course Categories -->
                <a href="#"
                class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center hover:bg-purple-100 transition duration-200 group">
                    <i class="fas fa-tags text-purple-600 text-xl mb-2 group-hover:text-purple-700"></i>
                    <p class="text-sm font-medium text-purple-700">Categories</p>
                    <p class="text-xs text-purple-600 mt-1">Manage categories</p>
                </a>

                <!-- Course Reports -->
                <a href="#"
                class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center hover:bg-orange-100 transition duration-200 group">
                    <i class="fas fa-chart-bar text-orange-600 text-xl mb-2 group-hover:text-orange-700"></i>
                    <p class="text-sm font-medium text-orange-700">Reports</p>
                    <p class="text-xs text-orange-600 mt-1">Course analytics</p>
                </a>

            </div>
        </div>
        @endcan
        <!-- Popular Courses -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Popular Courses</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($popularCourses as $course)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-gray-800">{{ $course->course }}</h3>
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Active</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">{{ $course->description ? substr($course->description, 0, 80) . '...' : 'No description available' }}</p>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-blue-600 font-semibold">KSh {{ number_format($course->total_amount, 2) }}</span>
                        <span class="text-gray-500">{{ $course->training_duration }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Courses Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">All Courses</h2>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 p-6">

                <!-- SEARCH BAR -->
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input type="text"
                        id="courseSearch"
                        placeholder="Search courses by name, description..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                        autocomplete="off">

                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDE CONTROLS -->
                <div class="flex items-center space-x-4">

                    <!-- Count -->
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $courses->total() }} courses
                    </span>

                    <!-- Filters -->
                    <div class="flex space-x-2">

                        <!-- STATUS FILTER -->
                        <select id="statusFilter" onchange="filterCourses()"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>

                        <!-- SORT FILTER -->
                        <select id="sortFilter" onchange="filterCourses()"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="fee_high">Fee: High to Low</option>
                            <option value="fee_low">Fee: Low to High</option>
                            <option value="name_asc">Name: A-Z</option>
                            <option value="name_desc">Name: Z-A</option>
                        </select>

                    </div>
                </div>
            </div>

            <!-- Search results container -->
            <div id="searchResults" class="hidden mb-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div id="searchResultsContent"></div>
            </div>

            <!-- Courses Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                   <!-- In your courses table, update the headers and columns -->
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left">Category</th>
                            <th class="px-4 py-3 text-left">Course Name</th>
                            <th class="px-4 py-3 text-left">Level</th>
                            <th class="px-4 py-3 text-left">Duration</th>
                            <th class="px-4 py-3 text-left">Sessions</th>
                            <th class="px-4 py-3 text-left">Breakdown</th>
                            <th class="px-4 py-3 text-left">Fee</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="coursesTableBody">
                        @foreach($courses as $course)
                        @php
                            $totalSessions = $course->practical_sessions + $course->theoretical_sessions;
                            $practicalPercent = $totalSessions > 0 ? round(($course->practical_sessions / $totalSessions) * 100) : 0;
                            $theoreticalPercent = $totalSessions > 0 ? round(($course->theoretical_sessions / $totalSessions) * 100) : 0;
                        @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $course->category->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    @if($course->image)
                                    <img src="{{ Storage::url($course->image) }}"
                                        alt="{{ $course->course }}"
                                        class="w-10 h-10 rounded-lg object-cover mr-3">
                                    @else
                                    <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-book text-gray-400"></i>
                                    </div>
                                    @endif
                                    <span class="font-medium text-gray-800">{{ $course->course }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $course->level == 'basic' ? 'bg-green-100 text-green-800' :
                                    ($course->level == 'intermediate' ? 'bg-yellow-100 text-yellow-800' :
                                    'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($course->level) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $course->training_duration }}</td>
                            <td class="px-4 py-3">
                                <div class="text-center">
                                    <span class="font-semibold text-blue-600">{{ $totalSessions }}</span>
                                    <div class="text-xs text-gray-500">total sessions</div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-xs">
                                    <div class="flex justify-between mb-1">
                                        <span>Practical: {{ $course->practical_sessions }}</span>
                                        <span>{{ $practicalPercent }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $practicalPercent }}%"></div>
                                    </div>
                                    <div class="flex justify-between mt-1">
                                        <span>Theory: {{ $course->theoretical_sessions }}</span>
                                        <span>{{ $theoreticalPercent }}%</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-blue-600">KSh {{ number_format($course->total_amount, 2) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($course->status == 1)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Active</span>
                                @else
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    @can('edit courses')
                                    <button onclick="openEditModal({{ $course->id }})"
                                        class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    @endcan
                                    @can('delete courses')
                                    <button onclick="openDeleteModal({{ $course->id }})"
                                        class="text-red-600 hover:text-red-800 text-sm">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4 p-4">
                    {{ $courses->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Course Modal -->
<div id="courseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">
        <button onclick="closeCourseModal()" class="absolute top-3 right-3 text-gray-600 hover:text-black">
            <i class="fas fa-times text-xl"></i>
        </button>

        <h2 class="text-xl font-semibold text-gray-800 mb-4">Add New Course</h2>

        <form id="courseForm" enctype="multipart/form-data">
            @csrf
             <!-- Category Selection -->
            <div class="mb-4">
                <label class="text-sm font-medium">Category *</label>
                <select name="category_id" id="category_id"
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Course Basic Info -->
            <div class="grid grid-cols-1 gap-4 mb-4">
                <div>
                    <label class="text-sm font-medium">Course Name *</label>
                    <input type="text" name="course" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label class="text-sm font-medium">Description</label>
                    <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" placeholder="Brief description of the course..."></textarea>
                </div>
            </div>
            <!-- In your course modal form, add this after training_duration -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="text-sm font-medium">Training Duration *</label>
                    <input type="text" name="training_duration" id="training_duration"
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g., 21 days" required
                        onchange="calculateSessions()">
                </div>

                <div>
                    <label class="text-sm font-medium">Practical Sessions *</label>
                    <input type="number" name="practical_sessions" id="practical_sessions"
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500"
                        min="0" required
                        onchange="updateSessionSummary()">
                </div>

                <div>
                    <label class="text-sm font-medium">Theoretical Sessions *</label>
                    <input type="number" name="theoretical_sessions" id="theoretical_sessions"
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500"
                        min="0" required
                        onchange="updateSessionSummary()">
                </div>
            </div>

            <!-- Session Summary -->
            <div id="sessionSummary" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
                <div class="flex justify-between items-center text-sm">
                    <span>Total Sessions: <strong id="totalSessions">0</strong></span>
                    <span>Practical: <strong id="practicalPercent">0%</strong></span>
                    <span>Theory: <strong id="theoreticalPercent">0%</strong></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div id="sessionProgress" class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full" style="width: 0%"></div>
                </div>
                <div id="sessionWarning" class="text-xs text-red-600 mt-1 hidden"></div>
            </div>
            <!-- Course Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">


                <div>
                    <label class="text-sm font-medium">Total Fee (KSh) *</label>
                    <input type="number" name="total_amount" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>
            <!-- Branch Selection (Admin only) -->
            @if(auth()->user()->isAdmin())
            <div class="mb-4">
                <label class="text-sm font-medium">Branch *</label>
                <select name="branch_id" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Branch</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $selectedBranch == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            <!-- Status and Image -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="text-sm font-medium">Status *</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Course Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Supported formats: JPEG, PNG, JPG, GIF (Max: 2MB)</p>
                </div>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white p-3 rounded-lg hover:bg-green-700 transition duration-200 font-semibold">
                Create Course
            </button>
        </form>
    </div>
</div>

<!-- Edit Course Modal -->
<div id="editCourseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">
        <button onclick="closeEditModal()" class="absolute top-3 right-3 text-gray-600 hover:text-black">
            <i class="fas fa-times text-xl"></i>
        </button>

        <h2 class="text-xl font-semibold text-gray-800 mb-4">Edit Course</h2>

        <form id="editCourseForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="course_id" id="edit_course_id">
            <!-- Category Selection -->
            <div class="mb-4">
                <label class="text-sm font-medium">Category *</label>
                <select name="category_id" id="category_id"
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Course Basic Info -->
            <div class="grid grid-cols-1 gap-4 mb-4">
                <div>
                    <label class="text-sm font-medium">Course Name *</label>
                    <input type="text" name="course" id="edit_course" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label class="text-sm font-medium">Description</label>
                    <textarea name="description" id="edit_description" rows="3" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>

            <!-- Course Details - ADD SESSION FIELDS HERE -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="text-sm font-medium">Training Duration *</label>
                    <input type="text" name="training_duration" id="edit_training_duration"
                           class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., 21 days" required
                           onchange="calculateSessions()">
                </div>

                <div>
                    <label class="text-sm font-medium">Practical Sessions *</label>
                    <input type="number" name="practical_sessions" id="edit_practical_sessions"
                           class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500"
                           min="0" required
                           onchange="updateSessionSummary()">
                </div>

                <div>
                    <label class="text-sm font-medium">Theoretical Sessions *</label>
                    <input type="number" name="theoretical_sessions" id="edit_theoretical_sessions"
                           class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500"
                           min="0" required
                           onchange="updateSessionSummary()">
                </div>
            </div>

            <!-- Session Summary for Edit Modal -->
            <div id="editSessionSummary" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
                <div class="flex justify-between items-center text-sm">
                    <span>Total Sessions: <strong id="editTotalSessions">0</strong></span>
                    <span>Practical: <strong id="editPracticalPercent">0%</strong></span>
                    <span>Theory: <strong id="editTheoreticalPercent">0%</strong></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div id="editSessionProgress" class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full" style="width: 0%"></div>
                </div>
                <div id="editSessionWarning" class="text-xs text-red-600 mt-1 hidden"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-sm font-medium">Total Fee (KSh) *</label>
                    <input type="number" name="total_amount" id="edit_total_amount" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>
            <!-- Branch Selection (Admin only) -->
            @if(auth()->user()->isAdmin())
            <div class="mb-4">
                <label class="text-sm font-medium">Branch *</label>
                <select name="branch_id" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Branch</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $selectedBranch == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            <!-- Status and Image -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="text-sm font-medium">Status *</label>
                    <select name="status" id="edit_status" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Course Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image</p>
                    <div id="currentImage" class="mt-2"></div>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold">
                Update Course
            </button>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Confirm Delete</h2>
        <p class="text-gray-600 mb-5">Are you sure you want to delete this course? This action cannot be undone.</p>
        <input type="hidden" id="deleteCourseId">
        <div class="flex justify-end gap-3">
            <button onclick="closeDeleteModal()" class="px-4 py-2 bg-blue-400 text-white rounded hover:bg-gray-500">
                Cancel
            </button>
            <button onclick="confirmDelete()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                Delete
            </button>
        </div>
    </div>
</div>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Course Modal Functions
function openCourseModal() {
    document.getElementById('courseModal').classList.remove('hidden');
}

function closeCourseModal() {
    document.getElementById('courseModal').classList.add('hidden');
    document.getElementById('courseForm').reset();
}

// Edit Modal Functions
function openEditModal(courseId) {
    fetch(`/courses/${courseId}/edit`)
        .then(res => res.json())
        .then(data => {
            const course = data.course;
            const categories = data.categories;

            document.getElementById('edit_course_id').value = course.id;
            document.getElementById('edit_course').value = course.course;
            document.getElementById('edit_description').value = course.description || '';
            document.getElementById('edit_training_duration').value = course.training_duration;
            document.getElementById('edit_practical_sessions').value = course.practical_sessions || 0;
            document.getElementById('edit_theoretical_sessions').value = course.theoretical_sessions || 0;
            document.getElementById('edit_total_amount').value = course.total_amount;
            document.getElementById('edit_status').value = course.status;
            document.getElementById('edit_level').value = course.level;

            // Set category dropdown
            const categorySelect = document.getElementById('edit_category_id');
            categorySelect.innerHTML = '<option value="">Select Category</option>';
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                option.selected = category.id == course.category_id;
                categorySelect.appendChild(option);
            });

            // Set branch if admin
            @if(auth()->user()->isAdmin())
            document.getElementById('edit_branch_id').value = course.branch_id;
            @endif

            // Show current image if exists
            const currentImageDiv = document.getElementById('currentImage');
            if (course.image) {
                currentImageDiv.innerHTML = `
                    <p class="text-xs text-gray-600 mb-1">Current Image:</p>
                    <img src="/storage/${course.image}" alt="${course.course}" class="w-20 h-20 rounded-lg object-cover border">
                `;
            } else {
                currentImageDiv.innerHTML = '<p class="text-xs text-gray-600">No image uploaded</p>';
            }

            // Initialize session summary for edit modal
            updateEditSessionSummary();

            document.getElementById('editCourseModal').classList.remove('hidden');
        })
        .catch(err => toastr.error("Failed to load course details"));
}

// Session management functions for both modals
function calculateSessions() {
    const durationInput = document.getElementById('training_duration') || document.getElementById('edit_training_duration');
    const practicalInput = document.getElementById('practical_sessions') || document.getElementById('edit_practical_sessions');
    const theoreticalInput = document.getElementById('theoretical_sessions') || document.getElementById('edit_theoretical_sessions');

    // Extract number from duration (e.g., "21 days" -> 21)
    const durationMatch = durationInput.value.match(/(\d+)/);
    const totalSessions = durationMatch ? parseInt(durationMatch[1]) : 0;

    if (totalSessions > 0 && practicalInput && theoreticalInput) {
        // Auto-suggest breakdown based on course name
        const courseName = document.getElementById('edit_course')?.value.toLowerCase() ||
                          document.querySelector('[name="course"]')?.value.toLowerCase() || '';

        let practicalRatio = 0.65; // Default 65% practical

        if (courseName.includes('beginner') || courseName.includes('basic')) {
            practicalRatio = 0.70;
        } else if (courseName.includes('commercial') || courseName.includes('heavy')) {
            practicalRatio = 0.60;
        } else if (courseName.includes('motorcycle')) {
            practicalRatio = 0.80;
        } else if (courseName.includes('refresher')) {
            practicalRatio = 0.90;
        }

        const practicalSessions = Math.round(totalSessions * practicalRatio);
        const theoreticalSessions = totalSessions - practicalSessions;

        practicalInput.value = practicalSessions;
        theoreticalInput.value = theoreticalSessions;

        updateSessionSummary();
        updateEditSessionSummary();
    }
}

function updateSessionSummary() {
    const practicalSessions = parseInt(document.getElementById('practical_sessions')?.value) || 0;
    const theoreticalSessions = parseInt(document.getElementById('theoretical_sessions')?.value) || 0;
    const totalSessions = practicalSessions + theoreticalSessions;

    const durationInput = document.getElementById('training_duration');
    const durationMatch = durationInput?.value.match(/(\d+)/);
    const expectedSessions = durationMatch ? parseInt(durationMatch[1]) : 0;

    const summaryDiv = document.getElementById('sessionSummary');
    const warningDiv = document.getElementById('sessionWarning');

    if (totalSessions > 0 && summaryDiv) {
        summaryDiv.classList.remove('hidden');

        const practicalPercent = Math.round((practicalSessions / totalSessions) * 100);
        const theoreticalPercent = Math.round((theoreticalSessions / totalSessions) * 100);

        document.getElementById('totalSessions').textContent = totalSessions;
        document.getElementById('practicalPercent').textContent = practicalPercent + '%';
        document.getElementById('theoreticalPercent').textContent = theoreticalPercent + '%';

        // Update progress bar
        document.getElementById('sessionProgress').style.width = practicalPercent + '%';

        // Show warning if sessions don't match duration
        if (expectedSessions > 0 && totalSessions !== expectedSessions) {
            warningDiv.classList.remove('hidden');
            warningDiv.textContent = `Warning: Total sessions (${totalSessions}) don't match training duration (${expectedSessions} sessions)`;
        } else {
            warningDiv.classList.add('hidden');
        }
    } else if (summaryDiv) {
        summaryDiv.classList.add('hidden');
    }
}

function updateEditSessionSummary() {
    const practicalSessions = parseInt(document.getElementById('edit_practical_sessions')?.value) || 0;
    const theoreticalSessions = parseInt(document.getElementById('edit_theoretical_sessions')?.value) || 0;
    const totalSessions = practicalSessions + theoreticalSessions;

    const durationInput = document.getElementById('edit_training_duration');
    const durationMatch = durationInput?.value.match(/(\d+)/);
    const expectedSessions = durationMatch ? parseInt(durationMatch[1]) : 0;

    const summaryDiv = document.getElementById('editSessionSummary');
    const warningDiv = document.getElementById('editSessionWarning');

    if (totalSessions > 0 && summaryDiv) {
        summaryDiv.classList.remove('hidden');

        const practicalPercent = Math.round((practicalSessions / totalSessions) * 100);
        const theoreticalPercent = Math.round((theoreticalSessions / totalSessions) * 100);

        document.getElementById('editTotalSessions').textContent = totalSessions;
        document.getElementById('editPracticalPercent').textContent = practicalPercent + '%';
        document.getElementById('editTheoreticalPercent').textContent = theoreticalPercent + '%';

        // Update progress bar
        document.getElementById('editSessionProgress').style.width = practicalPercent + '%';

        // Show warning if sessions don't match duration
        if (expectedSessions > 0 && totalSessions !== expectedSessions) {
            warningDiv.classList.remove('hidden');
            warningDiv.textContent = `Warning: Total sessions (${totalSessions}) don't match training duration (${expectedSessions} sessions)`;
        } else {
            warningDiv.classList.add('hidden');
        }
    } else if (summaryDiv) {
        summaryDiv.classList.add('hidden');
    }
}

// Initialize session summary when modal opens
function initializeSessionFields() {
    setTimeout(updateSessionSummary, 100);
}

function initializeEditSessionFields() {
    setTimeout(updateEditSessionSummary, 100);
}

// Update your existing modal open functions
function openCourseModal() {
    document.getElementById('courseModal').classList.remove('hidden');
    setTimeout(initializeSessionFields, 100);
}
function closeEditModal() {
    document.getElementById('editCourseModal').classList.add('hidden');
}

// Delete Modal Functions
function openDeleteModal(courseId) {
    document.getElementById('deleteCourseId').value = courseId;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Form Submissions
document.getElementById('courseForm').addEventListener('submit', function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch("{{ route('courses.store') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: formData
    })
    .then(async res => {
        let data = await res.json();

        if (res.ok && data.success) {
            toastr.success("Course created successfully!");
            closeCourseModal();
            setTimeout(() => location.reload(), 600);
        }
        else if (res.status === 422) {
            let errors = data.errors;
            Object.keys(errors).forEach((key) => {
                toastr.error(errors[key][0]);
            });
        }
        else {
            toastr.error(data.message || "An unexpected error occurred.");
        }
    })
    .catch(err => {
        console.error(err);
        toastr.error("Something went wrong. Please try again.");
    });
});

document.getElementById('editCourseForm').addEventListener('submit', function(e){
    e.preventDefault();

    let courseId = document.getElementById('edit_course_id').value;
    let formData = new FormData(this);

    fetch(`/courses/${courseId}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "X-HTTP-Method-Override": "PUT"
        },
        body: formData
    })
    .then(async res => {
        let data = await res.json();

        if (res.ok && data.success) {
            toastr.success("Course updated successfully");
            setTimeout(() => location.reload(), 600);
        }
        else toastr.error(data.message);
    })
    .catch(() => toastr.error("Update failed"));
});

function confirmDelete() {
    let courseId = document.getElementById('deleteCourseId').value;

    fetch(`/courses/${courseId}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        }
    })
    .then(async res => {
        let data = await res.json();

        if (res.ok && data.success) {
            toastr.success("Course deleted successfully");
            closeDeleteModal();
            setTimeout(() => location.reload(), 600);
        } else {
            toastr.error(data.message || "Delete failed");
        }
    })
    .catch(() => toastr.error("Server error"));
}

// Search and Filter Functions
let searchTimeout;

function filterCourses() {
    const q = document.getElementById("courseSearch").value.trim();
    const status = document.getElementById("statusFilter").value;
    const sort = document.getElementById("sortFilter").value;

    console.log('Searching with:', { q, status, sort }); // Debug log

    // Show loading state
    const tbody = document.getElementById("coursesTableBody");
    tbody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center py-4 text-gray-500">
                <i class="fas fa-spinner fa-spin mr-2"></i>Searching...
            </td>
        </tr>
    `;

    // Build URL with parameters
    const params = new URLSearchParams();
    if (q) params.append('q', q);
    if (status !== 'all') params.append('status', status);
    if (sort !== 'newest') params.append('sort', sort);

    const url = `/courses/search?${params.toString()}`;
    console.log('Fetching URL:', url); // Debug log

    fetch(url)
        .then(res => {
            console.log('Response status:', res.status); // Debug log
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            console.log('Search response:', data); // Debug log
            if (data.success) {
                renderCourses(data.courses);
            } else {
                throw new Error(data.message || 'Search returned unsuccessful');
            }
        })
        .catch(error => {
            console.error('Search error details:', error);

            // Show more specific error message
            let errorMessage = "Search failed. ";
            if (error.message.includes('404')) {
                errorMessage += "Search endpoint not found. ";
            } else if (error.message.includes('500')) {
                errorMessage += "Server error. ";
            }
            errorMessage += "Please try again or contact support.";

            toastr.error(errorMessage);

            // Show original courses instead of reloading
            showOriginalCourses();
        });
}

function showOriginalCourses() {
    // This will show the original server-rendered courses
    // You might need to store the original HTML or reload
    location.reload();
}

document.getElementById("courseSearch").addEventListener("keyup", function () {
    clearTimeout(searchTimeout);
    const query = this.value.trim();

    // Only search if query is empty or has at least 2 characters
    if (query === '' || query.length >= 2) {
        searchTimeout = setTimeout(() => filterCourses(), 400);
    }
});

// Also trigger filter when status or sort changes
document.getElementById("statusFilter").addEventListener("change", filterCourses);
document.getElementById("sortFilter").addEventListener("change", filterCourses);

function renderCourses(courses) {
    const tbody = document.getElementById("coursesTableBody");

    if (!tbody) {
        console.error('coursesTableBody not found');
        return;
    }

    tbody.innerHTML = "";

    if (!courses || courses.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-8 text-gray-500">
                    <i class="fas fa-search mr-2"></i>No courses found matching your search criteria
                </td>
            </tr>
        `;
        return;
    }

    const canEdit = {{ auth()->user()->can('edit courses') ? 'true' : 'false' }};
    const canDelete = {{ auth()->user()->can('delete courses') ? 'true' : 'false' }};

    courses.forEach(c => {
        const imageHtml = c.image ?
            `<img src="/storage/${c.image}" alt="${c.course_name}" class="w-10 h-10 rounded-lg object-cover mr-3">` :
            `<div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-book text-gray-400"></i>
            </div>`;

        tbody.innerHTML += `
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="flex items-center">
                        ${imageHtml}
                        <span class="font-medium text-gray-800">${c.course_name}</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">${c.duration}</td>
                <td class="px-4 py-3">
                    <div class="text-center">
                        <span class="font-semibold text-blue-600">${c.total_sessions}</span>
                        <div class="text-xs text-gray-500">total sessions</div>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <div class="text-xs">
                        <div class="flex justify-between mb-1">
                            <span>Practical: ${c.practical_sessions}</span>
                            <span>${c.practical_percent}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-blue-500 h-1.5 rounded-full" style="width: ${c.practical_percent}%"></div>
                        </div>
                        <div class="flex justify-between mt-1">
                            <span>Theory: ${c.theoretical_sessions}</span>
                            <span>${c.theoretical_percent}%</span>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <span class="font-semibold text-blue-600">KSh ${c.fee}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="${c.status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} px-2 py-1 rounded-full text-xs font-medium">${c.status}</span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        ${canEdit ? `
                            <button onclick="openEditModal(${c.id})" class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-edit"></i> Edit
                            </button>` : ''}
                        ${canDelete ? `
                            <button onclick="openDeleteModal(${c.id})" class="text-red-600 hover:text-red-800 text-sm">
                                <i class="fas fa-trash"></i> Delete
                            </button>` : ''}
                    </div>
                </td>
            </tr>
        `;
    });
}

// Test function to check if search is working
function testSearch() {
    console.log('Testing search functionality...');
    fetch('/courses/search?q=test&status=all&sort=newest')
        .then(res => res.json())
        .then(data => console.log('Test search result:', data))
        .catch(err => console.error('Test search error:', err));
}

// Call this in browser console to test
window.testSearch = testSearch;

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Courses page loaded');
    // Test search on load (optional)
    // testSearch();
});
// Session management functions
function calculateSessions() {
    const durationInput = document.getElementById('training_duration');
    const practicalInput = document.getElementById('practical_sessions');
    const theoreticalInput = document.getElementById('theoretical_sessions');

    // Extract number from duration (e.g., "21 days" -> 21)
    const durationMatch = durationInput.value.match(/(\d+)/);
    const totalSessions = durationMatch ? parseInt(durationMatch[1]) : 0;

    if (totalSessions > 0) {
        // Auto-suggest breakdown based on course name
        const courseName = document.getElementById('edit_course')?.value.toLowerCase() ||
                          document.querySelector('[name="course"]')?.value.toLowerCase() || '';

        let practicalRatio = 0.65; // Default 65% practical

        if (courseName.includes('beginner') || courseName.includes('basic')) {
            practicalRatio = 0.70;
        } else if (courseName.includes('commercial') || courseName.includes('heavy')) {
            practicalRatio = 0.60;
        } else if (courseName.includes('motorcycle')) {
            practicalRatio = 0.80;
        } else if (courseName.includes('refresher')) {
            practicalRatio = 0.90;
        }

        const practicalSessions = Math.round(totalSessions * practicalRatio);
        const theoreticalSessions = totalSessions - practicalSessions;

        practicalInput.value = practicalSessions;
        theoreticalInput.value = theoreticalSessions;

        updateSessionSummary();
    }
}

function updateSessionSummary() {
    const practicalSessions = parseInt(document.getElementById('practical_sessions').value) || 0;
    const theoreticalSessions = parseInt(document.getElementById('theoretical_sessions').value) || 0;
    const totalSessions = practicalSessions + theoreticalSessions;

    const durationInput = document.getElementById('training_duration');
    const durationMatch = durationInput.value.match(/(\d+)/);
    const expectedSessions = durationMatch ? parseInt(durationMatch[1]) : 0;

    const summaryDiv = document.getElementById('sessionSummary');
    const warningDiv = document.getElementById('sessionWarning');

    if (totalSessions > 0) {
        summaryDiv.classList.remove('hidden');

        const practicalPercent = Math.round((practicalSessions / totalSessions) * 100);
        const theoreticalPercent = Math.round((theoreticalSessions / totalSessions) * 100);

        document.getElementById('totalSessions').textContent = totalSessions;
        document.getElementById('practicalPercent').textContent = practicalPercent + '%';
        document.getElementById('theoreticalPercent').textContent = theoreticalPercent + '%';

        // Update progress bar
        document.getElementById('sessionProgress').style.width = practicalPercent + '%';

        // Show warning if sessions don't match duration
        if (expectedSessions > 0 && totalSessions !== expectedSessions) {
            warningDiv.classList.remove('hidden');
            warningDiv.textContent = `Warning: Total sessions (${totalSessions}) don't match training duration (${expectedSessions} sessions)`;
        } else {
            warningDiv.classList.add('hidden');
        }
    } else {
        summaryDiv.classList.add('hidden');
    }
}

// Initialize session summary when modal opens
function initializeSessionFields() {
    setTimeout(updateSessionSummary, 100);
}

// Update your existing modal open functions
function openCourseModal() {
    document.getElementById('courseModal').classList.remove('hidden');
    setTimeout(initializeSessionFields, 100);
}


</script>

@endsection
@endcan
