@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
@endphp

@can('view students')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    @php
    $actionButtons = [];

    if (auth()->user()->can('add students')) {
        $actionButtons[] = [
            'text' => 'Add Student',
            'onclick' => 'openEnrollmentModal()',
            'icon' => 'fas fa-plus',
            'color' => 'bg-green-600 hover:bg-green-700'
        ];
    }
    @endphp

    @include('components.dashboard-header', [
        'title' => 'Learners Corner',
        'subtitle' => 'Manage all your learners in this corner',
        'actionButtons' => $actionButtons
    ])

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">

            <!-- Total Students -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Students</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalStudents }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Students -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Active Students</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $activeStudents }}</p>
                    </div>
                </div>
            </div>

            <!-- Suspended Students -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-times text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Suspended Students</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $suspendedStudents }}</p>
                    </div>
                </div>
            </div>

            <!-- Alumni Students -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Alumni Students</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $alumniStudents }}</p>
                    </div>
                </div>
            </div>

        </div>


        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                <!-- Applications -->
                <a href="#"
                class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center hover:bg-blue-100 transition duration-200 group">
                    <i class="fas fa-folder-open text-blue-600 text-xl mb-2 group-hover:text-blue-700"></i>
                    <p class="text-sm font-medium text-blue-700">Applications</p>
                    <p class="text-xs text-blue-600 mt-1">View applications</p>
                </a>

                <!-- Enrollment -->
                <a href="javascript:void(0)"
                onclick="openEnrollmentModal()"
                class="bg-green-50 border border-green-200 rounded-lg p-4 text-center hover:bg-green-100 transition duration-200 group">
                    <i class="fas fa-user-check text-green-600 text-xl mb-2 group-hover:text-green-700"></i>
                    <p class="text-sm font-medium text-green-700">Enrollment</p>
                    <p class="text-xs text-green-600 mt-1">Enroll students</p>
                </a>


                <!-- Students List -->
                <a href="{{ route('students.index') }}"
                class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center hover:bg-purple-100 transition duration-200 group">
                    <i class="fas fa-users text-purple-600 text-xl mb-2 group-hover:text-purple-700"></i>
                    <p class="text-sm font-medium text-purple-700">Students</p>
                    <p class="text-xs text-purple-600 mt-1">Manage students</p>
                </a>

                <!-- Payments -->
                <a href="{{ route('payments.index') }}"
                class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center hover:bg-orange-100 transition duration-200 group">
                    <i class="fas fa-credit-card text-orange-600 text-xl mb-2 group-hover:text-orange-700"></i>
                    <p class="text-sm font-medium text-orange-700">Payments</p>
                    <p class="text-xs text-orange-600 mt-1">View payments</p>
                </a>

            </div>
        </div>


        <!--  Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">Recent Learners</h2>

                </div>
            </div>

            <!-- Add this after the Section header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">

                <!-- SEARCH BAR -->
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input type="text"
                        id="studentSearch"
                        placeholder="Search students by name, ID number, contact..."
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
                        {{ $students->total() }} learners
                    </span>

                    <!-- Filters -->
                    <div class="flex space-x-2">

                        <!-- STATUS FILTER -->
                        <select id="statusFilter" onchange="filterStudents()"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="dropped">Dropped</option>
                        </select>

                        <!-- SORT FILTER -->
                        <select id="sortFilter" onchange="filterStudents()"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="fee_high">Fee: High to Low</option>
                            <option value="fee_low">Fee: Low to High</option>
                        </select>

                    </div>
                </div>
            </div>

            <!-- Add search results container -->
            <div id="searchResults" class="hidden mb-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div id="searchResultsContent"></div>
            </div>

            <!-- Students Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Course</th>
                            <th class="px-4 py-2">Reg No</th>
                            <th class="px-4 py-2">Total Fee</th>
                            <th class="px-4 py-2">Paid</th>
                            <th class="px-4 py-2">Balance</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="studentsTableBody">
                        @foreach($students as $ef)
                        @php
                            $totalPaid = $ef->payments->sum('amount');
                            $balance = $ef->total_fee - $totalPaid;
                        @endphp
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $ef->student->name }}</td>
                            <td class="px-4 py-2">{{ $ef->course->course }}</td>
                            <td class="px-4 py-2">{{ $ef->ef_no }}</td>
                            <td class="px-4 py-2">{{ number_format($ef->total_fee, 2) }}</td>
                            <td class="px-4 py-2 text-green-600">{{ number_format($totalPaid, 2) }}</td>
                            <td class="px-4 py-2 text-red-600">{{ number_format($balance, 2) }}</td>
                            <td class="px-4 py-2 flex gap-2">
                                <!-- Edit -->
                                <button onclick="openEditModal({{ $ef->id }})"
                                    class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i> Edit
                                </button>

                            <button onclick="openDeleteModal({{ $ef->id }})"
                                class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i> Delete
                            </button>

                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $students->links() }}
                </div>

            </div>


        </div>
    </div>
</div>

<!-- Enrollment Modal -->
<div id="enrollmentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">

        <!-- Close Button -->
        <button onclick="closeEnrollmentModal()" class="absolute top-3 right-3 text-gray-600 hover:text-black">
            <i class="fas fa-times text-xl"></i>
        </button>

        <h2 class="text-xl font-semibold text-gray-800 mb-4">Student Enrollment</h2>

        <form id="enrollmentForm">

            <!-- STUDENT BASIC INFO -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                <div>
                    <label class="text-sm font-medium">ID / Birth / Passport Number</label>
                    <input type="text" name="id_no" class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="text-sm font-medium">Full Name</label>
                    <input type="text" name="name" class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="text-sm font-medium">Contact</label>
                    <input type="text" name="contact" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="text-sm font-medium">Email</label>
                    <input type="email" name="email" class="w-full border rounded p-2">
                </div>
            </div>

            <!-- ADDRESS -->
            <div class="mb-4">
                <label class="text-sm font-medium">Address</label>
                <input type="text" name="address" class="w-full border rounded p-2">
            </div>

            <!-- EF NUMBER -->
            <div class="mb-4">
                <label class="text-sm font-medium">EF Number</label>
                <input type="text" name="ef_no" class="w-full border rounded p-2" required>
            </div>

            <!-- COURSE -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                <div>
                    <label class="text-sm font-medium">Course</label>
                    <select name="course_id" id="courseSelect"
                            class="w-full border rounded p-2" required>
                        <option value="">-- choose course --</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}"
                                    data-fee="{{ $course->total_amount }}"
                                    data-duration="{{ $course->training_duration }}">
                                {{ $course->course }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Fee (Auto)</label>
                    <input type="number" name="total_fee" id="courseFee"
                        class="w-full border rounded p-2">
                </div>
            </div>

            <!-- TOTAL CLASSES (Auto from DB but editable) -->
            <div class="mb-4">
                <label class="text-sm font-medium">Number of Classes</label>
                <input type="number" name="total_class" id="totalClass"
                    class="w-full border rounded p-2" required>
            </div>


            <!-- Hidden Fields -->
            <input type="hidden" name="type" value="3">

            <button type="submit"
                    class="w-full bg-green-600 text-white p-3 rounded hover:bg-green-700">
                Enroll Student
            </button>

        </form>
    </div>
</div>
<div id="editStudentModal"
     class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">

        <button onclick="closeEditModal()" class="absolute top-3 right-3 text-gray-600 hover:text-black">
            <i class="fas fa-times text-xl"></i>
        </button>

        <h2 class="text-xl font-semibold mb-4">Edit Student Enrollment</h2>

        <form id="editStudentForm">

            <input type="hidden" name="ef_id" id="edit_ef_id">

            <!-- BASIC INFO -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-sm font-medium">ID Number</label>
                    <input type="text" name="id_no" id="edit_id_no" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="text-sm font-medium">Full Name</label>
                    <input type="text" name="name" id="edit_name" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="text-sm font-medium">Contact</label>
                    <input type="text" name="contact" id="edit_contact" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="text-sm font-medium">Email</label>
                    <input type="email" name="email" id="edit_email" class="w-full border rounded p-2">
                </div>
            </div>

            <!-- ADDRESS -->
            <div class="mb-4">
                <label class="text-sm font-medium">Address</label>
                <input type="text" name="address" id="edit_address" class="w-full border rounded p-2">
            </div>

            <!-- EF NUMBER -->
            <div class="mb-4">
                <label class="text-sm font-medium">EF Number</label>
                <input type="text" name="ef_no" id="edit_ef_no" class="w-full border rounded p-2">
            </div>

            <!-- COURSE -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-sm font-medium">Course</label>
                    <select id="edit_course_id" name="course_id" class="w-full border rounded p-2">
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}"
                                    data-fee="{{ $course->total_amount }}"
                                    data-duration="{{ $course->training_duration }}">
                                {{ $course->course }}
                            </option>
                        @endforeach
                    </select>

                </div>

                <div>
                    <label class="text-sm font-medium">Total Fee</label>
                    <input type="number" name="total_fee" id="edit_total_fee"
                           class="w-full border rounded p-2">
                </div>
            </div>

            <!-- TOTAL CLASSES -->
            <div class="mb-4">
                <label class="text-sm font-medium">Number of Classes</label>
                <input type="number" name="total_class" id="edit_total_class"
                       class="w-full border rounded p-2">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded hover:bg-blue-700">
                Save Changes
            </button>

        </form>
    </div>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">

        <h2 class="text-lg font-semibold text-gray-800 mb-3">Confirm Delete</h2>
        <p class="text-gray-600 mb-5">Are you sure you want to delete this learner permanently?</p>

        <input type="hidden" id="deleteEfId">

        <div class="flex justify-end gap-3">
            <button onclick="closeDeleteModal()"
                class="px-4 py-2 bg-blue-400 text-white rounded hover:bg-gray-500">
                Cancel
            </button>

            <button onclick="confirmDelete()"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                Delete
            </button>
        </div>

    </div>
</div>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function openEnrollmentModal() {
    document.getElementById('enrollmentModal').classList.remove('hidden');
}

function closeEnrollmentModal() {
    document.getElementById('enrollmentModal').classList.add('hidden');
}

// Autofill fee when course is selected
document.getElementById('courseSelect').addEventListener('change', function () {
    let fee = this.selectedOptions[0].getAttribute('data-fee');
    let duration = this.selectedOptions[0].getAttribute('data-duration');

    document.getElementById('courseFee').value = fee ? fee : '';
    document.getElementById('totalClass').value = duration ? duration : '';
});


document.getElementById('enrollmentForm').addEventListener('submit', function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch("{{ route('students.store') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: formData
    })
    .then(async res => {
        let data = await res.json();

        if (res.ok && data.success) {
            toastr.success("Student enrolled successfully!");
            closeEnrollmentModal();
            // Optional small delay before reload for a nicer user experience
            setTimeout(() => location.reload(), 600);
        }
        else if (res.status === 422) {
            // Validation errors
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

</script>

<script>
// OPEN EDIT MODAL
function openEditModal(efId) {
    fetch(`/students/${efId}/edit`)
        .then(res => res.json())
        .then(data => {

            // EF fields
            document.getElementById('edit_ef_id').value = data.ef.id;
            document.getElementById('edit_ef_no').value = data.ef.ef_no;
            document.getElementById('edit_total_fee').value = data.ef.total_fee;
            document.getElementById('edit_total_class').value = data.ef.total_class;

            // Select correct course
            document.getElementById('edit_course_id').value = data.ef.course_id;

            // STUDENT fields
            document.getElementById('edit_id_no').value = data.student.id_no;
            document.getElementById('edit_name').value = data.student.name;
            document.getElementById('edit_contact').value = data.student.contact ?? '';
            document.getElementById('edit_email').value = data.student.email ?? '';
            document.getElementById('edit_address').value = data.student.address ?? '';

            document.getElementById('editStudentModal').classList.remove('hidden');
        })
        .catch(err => toastr.error("Failed to load student details"));
}
document.getElementById('edit_course_id').addEventListener('change', function () {
    let option = this.selectedOptions[0];

    let fee = option.getAttribute('data-fee');
    let duration = option.getAttribute('data-duration');

    document.getElementById('edit_total_fee').value = fee ? fee : '';
    document.getElementById('edit_total_class').value = duration ? duration : '';
});

function closeEditModal() {
    document.getElementById('editStudentModal').classList.add('hidden');
}


// SUBMIT EDIT FORM
document.getElementById('editStudentForm').addEventListener('submit', function(e){
    e.preventDefault();

    let efId = document.getElementById('edit_ef_id').value;
    let formData = new FormData(this);

    fetch(`/students/${efId}`, {
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
            toastr.success("Student updated successfully");
            setTimeout(() => location.reload(), 600);
        }
        else toastr.error(data.message);
    })
    .catch(() => toastr.error("Update failed"));
});


// OPEN MODAL
function openDeleteModal(efId) {
    document.getElementById('deleteEfId').value = efId;
    document.getElementById('deleteModal').classList.remove('hidden');
}

// CLOSE MODAL
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// CONFIRM DELETE
function confirmDelete() {
    let efId = document.getElementById('deleteEfId').value;

    fetch(`/students/${efId}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        }
    })
    .then(async res => {
        let data = await res.json();

        if (res.ok) {
            toastr.success("Student deleted successfully");
            closeDeleteModal();

            // Remove row smoothly (if you added row IDs)
            let row = document.getElementById(`row-${efId}`);
            if (row) row.remove();

            setTimeout(() => location.reload(), 600);
        } else {
            toastr.error(data.message || "Delete failed");
        }
    })
    .catch(() => toastr.error("Server error"));
}
function filterStudents() {
    let q = document.getElementById("studentSearch").value.trim();
    let status = document.getElementById("statusFilter").value;
    let sort = document.getElementById("sortFilter").value;

    fetch(`/students/search?q=${encodeURIComponent(q)}&status=${status}&sort=${sort}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderStudents(data.students);
            }
        })
        .catch(() => toastr.error("Filter failed"));
}

let timeout = null;



let searchTimeout;
document.getElementById("studentSearch").addEventListener("keyup", function () {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        let q = this.value.trim();
        fetch(`/students/search?q=${encodeURIComponent(q)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) renderStudents(data.students);
            })
            .catch(() => toastr.error("Search failed"));
    }, 400);
});

function renderStudents(students) {
    let tbody = document.querySelector("table tbody");
    tbody.innerHTML = "";

    if (students.length === 0) {
        tbody.innerHTML = `
            <tr><td colspan="7" class="text-center py-4 text-gray-500">
                No matching students found
            </td></tr>`;
        return;
    }

    students.forEach(s => {
        tbody.innerHTML += `
            <tr class="border-b">
                <td class="px-4 py-2">${s.student_name}</td>
                <td class="px-4 py-2">${s.course_name}</td>
                <td class="px-4 py-2">${s.ef_no}</td>
                <td class="px-4 py-2">${s.total_fee}</td>
                <td class="px-4 py-2 text-green-600">${s.total_paid}</td>
                <td class="px-4 py-2 text-red-600">${s.balance}</td>
                <td class="px-4 py-2 flex gap-2">
                    <button onclick="openEditModal(${s.id})"
                        class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-edit"></i> Edit
                    </button>

                    <button onclick="openDeleteModal(${s.id})"
                        class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </td>
            </tr>
        `;
    });
}



</script>


<style>
.step {
    transition: all 0.3s ease;
}
.step.hidden {
    display: none;
}
.step.active {
    display: block;
}
</style>

@endsection
@endcan
