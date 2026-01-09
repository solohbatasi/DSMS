<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\VotingController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Api\LiveActivityController;
use App\Http\Controllers\Api\SearchApiController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\AttendeeController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\VotingCategoryController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\PesapalController;
use App\Http\Controllers\NomineeCategoryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MpesaPaymentController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\MeterController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\TicketDownloadController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\LessonApplicationController;
use App\Http\Controllers\AdminTicketController;



use App\Models\Customer;


/*
|--------------------------------------------------------------------------
| EMAIL VERIFICATION (JETSTREAM / FORTIFY)
|--------------------------------------------------------------------------
*/

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->intended('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (ROLE-BASED)
|--------------------------------------------------------------------------
*/



/*
|--------------------------------------------------------------------------
| M-PESA Payment Routes
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::post('/contact', [ContactMessageController::class, 'store'])->name('contact.store');

Route::get('/book-lessons', [LessonApplicationController::class, 'create'])->name('book-lessons');
Route::post('/book-lessons', [LessonApplicationController::class, 'store'])->name('applications.store');
Route::get('/application-success/{applicationNumber}', [LessonApplicationController::class, 'success'])->name('application.success');

Route::get('/driving-courses', [PageController::class, 'coursesIndex'])->name('pages.courses');



/*
|--------------------------------------------------------------------------
| PUBLIC API ROUTES
|--------------------------------------------------------------------------
*/



/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/


Route::middleware(['auth'])->group(function () {

    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        // Main profile page
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit'); // Alternative URL

        // Update profile info
        Route::patch('/update', [ProfileController::class, 'update'])->name('update');

        // Password management
        Route::get('/security', [ProfileController::class, 'security'])->name('security');
        Route::post('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        // Preferences
        Route::get('/preferences', [ProfileController::class, 'preferences'])->name('preferences');
        Route::patch('/preferences', [ProfileController::class, 'updatePreferences'])->name('preferences.update');

        // Activity
        Route::get('/activity', [ProfileController::class, 'activity'])->name('activity');

        // Account deletion
        Route::delete('/delete', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Dashboard (if needed)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Admin application management
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/applications', [LessonApplicationController::class, 'index'])->name('admin.applications.index');
    Route::get('/applications/{application}', [LessonApplicationController::class, 'show'])->name('admin.applications.show');
    Route::put('/applications/{application}/status', [LessonApplicationController::class, 'updateStatus'])->name('admin.applications.update-status');
});
// Admin contact message management routes
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/contact-messages', [ContactMessageController::class, 'index'])->name('admin.contact-messages.index');
    Route::get('/contact-messages/{contactMessage}', [ContactMessageController::class, 'show'])->name('admin.contact-messages.show');
    Route::post('/contact-messages/{contactMessage}/replied', [ContactMessageController::class, 'markAsReplied'])->name('admin.contact-messages.replied');
    Route::delete('/contact-messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('admin.contact-messages.destroy');
});
// payments.php routes
Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
Route::get('/payments/{id}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
Route::put('/payments/{id}', [PaymentController::class, 'update'])->name('payments.update');
Route::delete('/payments/{id}', [PaymentController::class, 'destroy'])->name('payments.destroy');
Route::get('/payments/search', [PaymentController::class, 'search'])->name('payments.search');
Route::get('/payments/search-students', [PaymentController::class, 'searchStudents'])->name('payments.search-students');

Route::get('/student/tickets', [App\Http\Controllers\DashboardController::class, 'ticketDownload'])->name('student.tickets');
Route::post('/student/tickets/download', [App\Http\Controllers\DashboardController::class, 'downloadTicket'])->name('student.tickets.download');
Route::post('/student/tickets/{ticket}/redownload', [App\Http\Controllers\DashboardController::class, 'redownloadTicket'])->name('student.tickets.redownload');
Route::get('/student/tickets/{ticket}/preview', [App\Http\Controllers\DashboardController::class, 'previewTicket'])->name('student.tickets.preview');  // routes/web.php

Route::get('/admin/students/{student}/tickets', [App\Http\Controllers\AdminTicketController::class, 'manageTickets'])->name('admin.students.tickets');
Route::post('/admin/students/{student}/tickets/download', [App\Http\Controllers\AdminTicketController::class, 'downloadTicketForStudent'])->name('admin.students.tickets.download');
Route::post('/admin/tickets/{ticket}/redownload', [App\Http\Controllers\AdminTicketController::class, 'redownloadTicket'])->name('admin.tickets.redownload');
Route::get('/admin/tickets/{ticket}/preview', [App\Http\Controllers\AdminTicketController::class, 'previewTicket'])->name('admin.tickets.preview');
// Instructor Routes
// Instructor Routes
Route::prefix('instructor')->group(function () {
    Route::get('/qr-verify', [App\Http\Controllers\InstructorController::class, 'showQRVerify'])->name('instructor.qr-verify');
    Route::post('/verify-ticket', [App\Http\Controllers\InstructorController::class, 'verifyTicket'])->name('instructor.verify-ticket');
    Route::post('/verify-ticket-manual', [App\Http\Controllers\InstructorController::class, 'verifyTicketManual'])->name('instructor.verify-ticket-manual');
});
// Receptionist Routes
Route::prefix('receptionist')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\ReceptionistController::class, 'index'])->name('receptionist.dashboard');

    // EF List Management
    Route::get('/ef-lists', [App\Http\Controllers\ReceptionistController::class, 'efLists'])->name('receptionist.ef-lists');
    Route::get('/ef-lists/create', [App\Http\Controllers\ReceptionistController::class, 'createEFList'])->name('receptionist.ef-lists.create');
    Route::post('/ef-lists', [App\Http\Controllers\ReceptionistController::class, 'storeEFList'])->name('receptionist.ef-lists.store');
    Route::post('/verify-ef-list', [App\Http\Controllers\ReceptionistController::class, 'verifyEFList'])->name('receptionist.verify-ef-list');

    // Student Management
    Route::get('/students', [App\Http\Controllers\ReceptionistController::class, 'students'])->name('receptionist.students');
    Route::get('/students/create', [App\Http\Controllers\ReceptionistController::class, 'createStudent'])->name('receptionist.students.create');
    Route::post('/students', [App\Http\Controllers\ReceptionistController::class, 'storeStudent'])->name('receptionist.students.store');

    // Payment Management
    Route::get('/payments', [App\Http\Controllers\ReceptionistController::class, 'payments'])->name('receptionist.payments');
    Route::get('/payments/create', [App\Http\Controllers\ReceptionistController::class, 'createPayment'])->name('receptionist.payments.create');
    Route::post('/payments', [App\Http\Controllers\ReceptionistController::class, 'storePayment'])->name('receptionist.payments.store');

    // Reports
    Route::get('/reports', [App\Http\Controllers\ReceptionistController::class, 'reports'])->name('receptionist.reports');
});
Route::prefix('students')->group(function () {
     Route::get('/search', [StudentController::class, 'search'])
        ->name('students.search');
    Route::get('/', [StudentController::class, 'index'])->name('students.index');
    Route::get('/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/', [StudentController::class, 'store'])->name('students.store');
    Route::get('/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
});

// Expenses Routes
Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
Route::get('/expenses/{id}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->name('expenses.update');
Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
Route::get('/expenses/search', [ExpenseController::class, 'search'])->name('expenses.search');

// Fetch user permissions
Route::get('/admin/users/{user}/permissions', [App\Http\Controllers\Admin\UserController::class, 'getPermissions'])
    ->name('admin.users.permissions');

// Update user permissions
Route::post('/admin/users/{user}/permissions', [App\Http\Controllers\Admin\UserController::class, 'updatePermissions'])
    ->name('admin.users.permissions.update');

    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
Route::post('/tickets/download', [TicketController::class, 'download'])->name('tickets.download');
Route::get('/tickets/history', [TicketController::class, 'history'])->name('tickets.history');

// Route::middleware(['auth', 'student'])->prefix('feedback')->group(function () {
    Route::get('/course-rating', [FeedbackController::class, 'courseRatingForm'])->name('feedback.course-rating');
    Route::post('/course-rating', [FeedbackController::class, 'storeCourseRating'])->name('feedback.course-rating.store');

    Route::get('/services', [FeedbackController::class, 'servicesFeedbackForm'])->name('feedback.services');
    Route::post('/services', [FeedbackController::class, 'storeServicesFeedback'])->name('feedback.services.store');

    Route::get('/thank-you', [FeedbackController::class, 'thankYou'])->name('feedback.thank-you');

Route::get('/courses/search', [CourseController::class, 'search'])->name('courses.search');
Route::resource('courses', CourseController::class);


});

/*
|--------------------------------------------------------------------------
| ORGANIZER ROUTES (ROLE-BASED)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:organizer'])->prefix('organizer')->group(function () {
    Route::get('/dashboard', [OrganizerController::class, 'dashboard'])->name('organizer.dashboard');

    // Event Management
    Route::get('/events', [OrganizerController::class, 'events'])->name('organizer.events');
    Route::get('/events/create', [OrganizerController::class, 'createEvent'])->name('organizer.events.create');
    Route::post('/events', [OrganizerController::class, 'storeEvent'])->name('events.store');
    Route::post('/event-categories', [OrganizerController::class, 'storeEventCategory'])->name('event-categories.store');
    Route::get('/organizer/events/{event}/edit', [OrganizerController::class, 'editEvent'])->name('organizer.events.edit');
    Route::put('/organizer/events/{event}', [OrganizerController::class, 'updateEvent'])->name('organizer.events.update');
    Route::get('/organizer/events/{event}', [OrganizerController::class, 'showEvent'])->name('organizer.events.show');
    Route::delete('/events/{event}', [OrganizerController::class, 'destroyEvent'])->name('organizer.events.destroy');

    // Voting Management
    Route::get('/voting', [OrganizerController::class, 'voting'])->name('organizer.voting');
    Route::get('/voting/create', [OrganizerController::class, 'createVoting'])->name('organizer.voting.create');
    Route::post('/voting', [OrganizerController::class, 'storeVoting'])->name('organizer.voting.store');
    Route::get('/voting/{contest}/edit', [OrganizerController::class, 'editVoting'])->name('organizer.voting.edit');
    Route::put('/voting/{contest}', [OrganizerController::class, 'updateVoting'])->name('organizer.voting.update');
    Route::delete('/voting/{contest}', [OrganizerController::class, 'destroyVoting'])->name('organizer.voting.destroy');
    Route::post('/voting/store', [VotingController::class, 'store'])->name('voting.store');
    Route::post('/categories/store', [VotingCategoryController::class, 'store'])
        ->name('categories.store');
    Route::post('/organizer/voting-category/store', [VotingCategoryController::class, 'store'])
    ->name('voting-category.store');

    Route::resource('nominee-categories', NomineeCategoryController::class);

    // Analytics
    Route::get('/analytics', [OrganizerController::class, 'analytics'])->name('organizer.analytics');
    Route::get('/analytics/events/{event}', [OrganizerController::class, 'eventAnalytics'])->name('organizer.analytics.event');
    Route::get('/analytics/voting/{contest}', [OrganizerController::class, 'votingAnalytics'])->name('organizer.analytics.voting');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (ROLE-BASED)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('dashboard', [UserController::class, 'index'])->name('dashboard');

    // User Management Routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'usersIndex'])->name('index');
        Route::get('/data', [UserController::class, 'getUsersData'])->name('data');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');

        // User Permissions & Status
        Route::get('/{user}/permissions', [UserController::class, 'getPermissions'])->name('permissions');
        Route::post('/{user}/permissions', [UserController::class, 'updatePermissions'])->name('permissions.update');
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{user}/toggle-verification', [UserController::class, 'toggleVerification'])->name('toggle-verification');
        Route::get('/{user}/stats', [UserController::class, 'getUserStats'])->name('stats');
    });

    // Role Management
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::get('/{role}/permissions', [RoleController::class, 'getPermissions'])->name('permissions');
    });





});


/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
Route::get('/payment/{type}/{id}', [PaymentController::class, 'process'])->name('payments.process');
Route::post('/payment/{type}/{id}/complete', [PaymentController::class, 'complete'])->name('payment.complete');
Route::patch('/organizer/events/{event}/status', [OrganizerController::class, 'updateEventStatus'])->name('organizer.events.update-status')->middleware(['auth', 'verified']);
Route::post('/admin/events/{event}/approve', [OrganizerController::class, 'approveEvent'])->name('admin.events.approve')->middleware(['auth', 'verified', 'can:admin']);
Route::delete('/organizer/events/{event}', [OrganizerController::class, 'destroyEvent'])->name('organizer.events.destroy')->middleware(['auth', 'verified']);
Route::get('/tickets/my-tickets', [TicketController::class, 'myTickets'])->name('tickets.my-tickets');
