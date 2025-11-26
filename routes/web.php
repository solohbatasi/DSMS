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


use App\Models\Customer;



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
// });
Route::get('/courses/search', [CourseController::class, 'search'])->name('courses.search');
Route::resource('courses', CourseController::class);
// web.php
Route::get('/bills/{bill}/info', [BillController::class, 'info']);
Route::get('/bills/info/customer/{customer}', [BillController::class, 'infoByCustomer']);
Route::get('/bills/info/meter/{meter}', [BillController::class, 'infoByMeter']);

Route::middleware(['auth'])->group(function () {
    Route::get('/bills', [BillController::class, 'index'])->name('bills.index');
    Route::post('/bills', [BillController::class, 'store'])->name('bills.store');
    Route::get('/bills/{bill}', [BillController::class, 'show'])->name('bills.show');
    Route::put('/bills/{bill}', [BillController::class, 'update'])->name('bills.edit');
    Route::delete('/bills/{bill}', [BillController::class, 'destroy'])->name('bills.destroy');
});

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
| NYAWASCO ROUTES
|--------------------------------------------------------------------------
*/

// Water Connection Routes
Route::get('/services/water-connection', function () {
    return view('services.water-connection');
})->name('water-connection');

// Show the form
Route::get('/water-connection/apply', [WaterConnectionController::class, 'create'])
    ->name('water-connection.apply');

// Handle form submission
Route::post('/water-connection/apply', [WaterConnectionController::class, 'store'])
    ->name('water-connection.submit');

Route::get('/services/sewer-connection', function () {
    return view('services.sewer-connection');
})->name('sewer-connection');

Route::get('/services/bill-payment', function () {
    return view('services.bill-payment');
})->name('bill-payment');

Route::get('/complaints/create', function () {
    return view('complaints.create');
})->name('complaints.create');

Route::get('/report-corruption', function () {
    return view('report-corruption');
})->name('corruption-report');

Route::get('/tenders', function () {
    return view('tenders.index');
})->name('tenders');

Route::get('/careers', function () {
    return view('careers.index');
})->name('careers');

Route::get('/reports', function () {
    return view('reports.index');
})->name('reports');

Route::get('/downloads', function () {
    return view('downloads.index');
})->name('downloads');

Route::get('/news', function () {
    return view('news.index');
})->name('news');

Route::get('/news/{slug}', function ($slug) {
    return view('news.show', compact('slug'));
})->name('news.show');

Route::get('/documentary', function () {
    return view('documentary');
})->name('documentary');

// Service Routes
Route::get('/services', function () {
    return view('services.index');
})->name('services');

Route::get('/services/water-supply', function () {
    return view('services.water-supply');
})->name('water-supply');

Route::get('/services/sewerage', function () {
    return view('services.sewerage');
})->name('sewerage');

Route::get('/services/new-connections', function () {
    return view('services.new-connections');
})->name('new-connections');

Route::get('/services/payments', function () {
    return view('services.payments');
})->name('payments');

Route::get('/support', function () {
    return view('support');
})->name('support');

// About Routes
Route::get('/about', function () {
    return view('about.index');
})->name('about');

Route::get('/about/management', function () {
    return view('about.management');
})->name('management');

// Projects Routes
Route::get('/projects', function () {
    return view('projects.index');
})->name('projects');

// Contact Form Submission
Route::post('/contact/submit', function (Request $request) {
    // For now, just redirect back with success message
    // You can implement email sending or database storage later
    return back()->with('success', 'Your message has been sent successfully!');
})->name('contact.submit');





/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (ROLE-BASED)
|--------------------------------------------------------------------------
*/


// Public water connection application routes
Route::get('/water-connection/apply', [WaterConnectionController::class, 'create'])->name('water-connection.apply');
Route::post('/water-connection/submit', [WaterConnectionController::class, 'store'])->name('water-connection.submit');

// Admin routes group
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // Water applications routes
    Route::get('/water-applications', [WaterConnectionController::class, 'index'])->name('water-applications.index');
    Route::get('/water-applications/{application}', [WaterConnectionController::class, 'show'])->name('water-applications.show');
    Route::post('/water-applications/{application}/approve', [WaterConnectionController::class, 'approve'])->name('water-applications.approve');
    Route::post('/water-applications/{application}/decline', [WaterConnectionController::class, 'decline'])->name('water-applications.decline');

    // Customers routes
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('dashboard', [UserController::class, 'index'])->name('dashboard');

    // Customer Management Routes
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');

        // Meter Readings
        Route::get('/{customer}/readings', [CustomerController::class, 'meterReadings'])->name('meter-readings');
        Route::get('/{customer}/readings/create', [CustomerController::class, 'createReading'])->name('readings.create');
        Route::post('/{customer}/readings', [CustomerController::class, 'storeReading'])->name('readings.store');

        // Billing
        Route::post('/{customer}/readings/{reading}/generate-bill', [CustomerController::class, 'generateBill'])->name('generate-bill');
        Route::get('/{customer}/bills', [CustomerController::class, 'bills'])->name('bills');

         Route::get('/customers/{customer}/address', function (Customer $customer) {
            return response()->json([
                'address' => $customer->physical_address
            ]);
        })->name('customers.address');
    });




    // Meter Readings Management
    Route::prefix('meter-readings')->name('meter-readings.')->group(function () {
        Route::get('/', [MeterReadingController::class, 'index'])->name('index');
        Route::get('/create', [MeterReadingController::class, 'create'])->name('create');
        Route::post('/', [MeterReadingController::class, 'store'])->name('store');
    });

    // Meters Management (Simple overview)
    Route::prefix('meters')->name('meters.')->group(function () {
        Route::get('/', [MeterController::class, 'index'])->name('index');
        Route::get('/{customer}', [MeterController::class, 'show'])->name('show');
    });

    // Meter Management Routes
    Route::prefix('meters')->name('meters.')->group(function () {
        Route::get('/', [MeterController::class, 'index'])->name('index');
        Route::get('/available', [MeterController::class, 'available'])->name('available');
        Route::get('/assigned', [MeterController::class, 'assigned'])->name('assigned');
        Route::get('/by-location', [MeterController::class, 'byLocation'])->name('by-location');

        Route::post('/', [MeterController::class, 'store'])->name('store');
        Route::get('/{meter}', [MeterController::class, 'show'])->name('show');
        Route::get('/{meter}/edit', [MeterController::class, 'edit'])->name('edit');
        Route::put('/{meter}', [MeterController::class, 'update'])->name('update');
        Route::post('/{meter}/assign', [MeterController::class, 'assignToCustomer'])->name('assign');
        Route::post('/{meter}/unassign', [MeterController::class, 'unassign'])->name('unassign');
    });




});



/*
|--------------------------------------------------------------------------
| M-PESA Payment Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/pay/mpesa/initiate', [MpesaPaymentController::class, 'initiatePayment'])->name('mpesa.initiate');
    Route::post('/pay/mpesa/check-status', [MpesaPaymentController::class, 'checkPaymentStatus'])->name('mpesa.checkStatus');
});

// M-PESA callback (no auth required)
Route::post('/payments/callback', [MpesaPaymentController::class, 'handleCallback'])->name('mpesa.callback');

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/category/{category}', [EventController::class, 'byCategory'])->name('events.byCategory');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/events/{event}/tickets', [EventController::class, 'tickets'])->name('events.tickets');

Route::get('/voting', [VotingController::class, 'index'])->name('voting.index');
Route::get('/voting/{contest}', [VotingController::class, 'show'])->name('voting.show');
Route::get('/voting/category/{category}', [VotingController::class, 'byCategory'])->name('voting.byCategory');

// Conditional Organizer Modal Logic (Frontend handles redirection if not authenticated)
Route::get('/organizers', [OrganizerController::class, 'index'])->name('organizers.index');
Route::get('/organizers/{organizer}', [OrganizerController::class, 'show'])->name('organizers.show');

Route::get('/search', [SearchController::class, 'index'])->name('search.index');

// Static Pages (Updated for NYAWASCO)
Route::view('/pricing', 'static.pricing', ['title' => 'Pricing - NYAWASCO'])->name('pricing');
Route::view('/contact', 'static.contact', ['title' => 'Contact Us - NYAWASCO'])->name('contact');
Route::view('/privacy', 'static.privacy', ['title' => 'Privacy Policy - NYAWASCO'])->name('privacy');
Route::view('/terms', 'static.terms', ['title' => 'Terms of Service - NYAWASCO'])->name('terms');
Route::view('/help', 'static.help', ['title' => 'Help Center - NYAWASCO'])->name('help');

Route::get('/events/{event}/tickets', [TicketController::class, 'show'])->name('tickets.purchase.show');
Route::post('/events/{event}/tickets/purchase', [TicketController::class, 'purchase'])->name('tickets.purchase');

/*
|--------------------------------------------------------------------------
| PUBLIC API ROUTES
|--------------------------------------------------------------------------
*/

Route::post('/pesapal/stkpush', [PesapalController::class, 'stkPush'])->name('pesapal.stkpush');
Route::post('/pesapal/callback', [PesapalController::class, 'callback'])->name('pesapal.callback');
Route::get('/pesapal/register-ipn', [PesapalController::class, 'registerIpn']);
Route::get('/pesapal/callback', [PesapalController::class, 'callbackReturn'])->name('pesapal.callback.return');
Route::post('/pesapal/ipn', [PesapalController::class, 'ipn'])->name('pesapal.ipn');

Route::prefix('api')->group(function () {
    Route::get('/live-activities', [LiveActivityController::class, 'index'])->name('api.live-activities');
    Route::get('/search', [SearchApiController::class, 'search'])->name('api.search');
    Route::get('/events/featured', [EventController::class, 'featured'])->name('api.events.featured');
    Route::get('/voting/featured', [VotingController::class, 'featured'])->name('api.voting.featured');
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tickets & Bookings
    Route::get('/my-tickets', [TicketController::class, 'myTickets'])->name('tickets.my-tickets');
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticketPurchase}', [TicketController::class, 'showTicket'])->name('tickets.show');
    Route::get('/tickets/{ticketPurchase}/download', [TicketController::class, 'download'])->name('tickets.download');
    Route::get('/tickets/{ticketPurchase}/view', [TicketController::class, 'view'])->name('tickets.view');

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/events/{event}/book', [BookingController::class, 'store'])->name('bookings.store');
    Route::delete('/bookings/{booking}', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Voting
    Route::post('/voting/{contest}/vote', [VotingController::class, 'vote'])->name('voting.vote');
    Route::get('/my-votes', [VotingController::class, 'myVotes'])->name('voting.myVotes');

    // Organizer Routes
    Route::prefix('organizer')->group(function () {
        Route::get('/dashboard', [OrganizerController::class, 'dashboard'])->name('organizer.dashboard');
        Route::get('/ticket-sales', [OrganizerController::class, 'ticketSales'])->name('organizer.ticket-sales');
        Route::get('/bookings', [OrganizerController::class, 'bookings'])->name('organizer.bookings');
    });
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

    Route::get('/attendees', [AttendeeController::class, 'index'])->name('attendees.index');
    Route::get('/attendees/{id}', [AttendeeController::class, 'show'])->name('attendees.show');
    Route::get('/attendees/{userId}/ticket-purchases', [AttendeeController::class, 'ticketPurchases'])->name('attendees.ticket-purchases');
    Route::get('/admin/attendees/data', [AttendeeController::class, 'getAttendeesData'])->name('attendees.data');
    Route::get('/admin/attendees/stats', [AttendeeController::class, 'getAttendeesStats'])->name('attendees.stats');

    // Vendor Management
    Route::prefix('vendors')->name('vendors.')->group(function () {
        // List all vendors
        Route::get('/', [VendorController::class, 'index'])->name('index');

        Route::get('/data', [VendorController::class, 'getVendorsData'])->name('data');
        Route::get('/stats', [VendorController::class, 'getVendorsStats'])->name('stats');
        Route::get('/{id}/details', [VendorController::class, 'getVendorDetails'])->name('details');

        // Optional: dashboard route for vendor admin overview
        Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('dashboard');
    });

    // Other admin management views
    Route::prefix('management')->name('management.')->group(function () {
        Route::view('/events', 'admin.events')->name('events');
        Route::view('/voting', 'admin.voting')->name('voting');
        Route::view('/tickets', 'admin.tickets')->name('tickets');
        Route::view('/analytics', 'admin.analytics')->name('analytics');
    });
});

/*
|--------------------------------------------------------------------------
| VENDOR ROUTES (ROLE-BASED)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', [VendorController::class, 'index'])->name('dashboard');
    Route::view('/services', 'vendor.services')->name('services');
    Route::view('/bookings', 'vendor.bookings')->name('bookings');
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
