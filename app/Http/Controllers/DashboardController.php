<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\VotingContest;
use App\Models\TicketPurchase;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\User;
use App\Models\Customer;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Meter;
use App\Models\WaterConnectionApplication;
use App\Models\Student_ef_list;
use App\Models\Booking;
use App\Models\VotePurchase;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Base data available for all dashboards
        $data = [
            'user' => $user,
            'recent_events' => Event::with('category')
                ->where('is_active', true)
                ->where('start_date', '>=', now())
                ->orderBy('start_date')
                ->take(5)
                ->paginate(10),

            'active_voting' => VotingContest::with('category')
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->where('end_date', '>=', now())
                        ->orWhereNull('end_date');
                })
                ->orderBy('end_date', 'asc')
                ->take(5)
                ->get(),
        ];

        // Safeguard for users without assigned roles
        if (!$user || !$user->role) {
            abort(403, 'Unauthorized: Role not assigned.');
        }

        // Role-specific logic
        switch ($user->role) {
            case 'admin':
                $data = array_merge($data, $this->getAdminData());
                return view('dashboard.admin', $data);

            case 'organizer':
                $data = array_merge($data, $this->getOrganizerData($user));
                return view('dashboard.organizer', $data);

            case 'vendor':
                $data = array_merge($data, $this->getVendorData($user));
                return view('dashboard.vendor', $data);

            default: // attendee or fallback
                $data = array_merge($data, $this->getAdminData($user));
                return view('dashboard.admin', $data);
        }
    }

private function getAdminData()
{
    // Customers
    $total_customers = Customer::count();
    $active_customers = Customer::where('status', 'active')->count();
    $inactive_customers = Customer::whereIn('status', ['inactive', 'suspended'])->count();
    $pending_connections = Customer::where('status', 'pending')->count();

    // Meters
    $total_meters = Meter::count();
    $installed_meters = Meter::whereNotNull('customer_id')->count();
    $available_meters = Meter::whereNull('customer_id')->count();
    $faulty_meters = Meter::where('status', '!=', 'available')->count();

    // Bills
    $total_bills = Bill::count();
    $paid_bills = Bill::where('bill_status', 'paid')->count();
    $unpaid_bills = Bill::where('bill_status', 'unpaid')->count();
    $overdue_bills = Bill::where('due_date', '<', now())
                         ->where('bill_status', 'unpaid')
                         ->count();

    // Payments & Revenue
    $total_revenue = Payment::sum('amount');
    $payments_today = Payment::whereDate('created_at', today())->sum('amount');
    $payments_this_month = Payment::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->sum('amount');
    $pending_payments = Payment::where('payment_status', 'pending')->count();

    // Payment methods breakdown
    $payment_methods = Payment::select('payment_method', DB::raw('SUM(amount) as total'))
                              ->groupBy('payment_method')
                              ->get();

    // Recent logs
    $recent_payments = Payment::with('bill', 'user')->latest()->take(5)->get();
    $recent_bills = Bill::with('customer')->latest()->take(5)->get();
    $total_users = User::count();
    $recent_users = User::latest()->paginate(10);
    $recent_customers = Customer::latest()->take(5)->get();

    // 🔥 New: Pending Approvals (water_connection_applications)
    $pending_approval_items = WaterConnectionApplication::where('status', 'pending')
                                ->latest()
                                ->take(10)
                                ->get();

    $pending_approvals = $pending_approval_items->count();
    // Monthly billed
    $monthly_billed = Bill::selectRaw('MONTH(billing_period_start) as month, SUM(total_amount) as total')
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

    $monthly_billed = array_replace(array_fill(1, 12, 0), $monthly_billed);

    // Monthly collected
    $monthly_collected = Payment::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

    $monthly_collected = array_replace(array_fill(1, 12, 0), $monthly_collected);

    // Bill status counts
    $bill_status_counts = [
        'paid' => Bill::where('bill_status', 'paid')->count(),
        'unpaid' => Bill::where('bill_status', 'unpaid')->count(),
        'overdue' => Bill::where('bill_status', 'unpaid')
                        ->where('due_date', '<', now())->count(),
    ];

    return [
        'total_customers'      => $total_customers,
        'active_customers'     => $active_customers,
        'inactive_customers'   => $inactive_customers,
        'pending_connections'  => $pending_connections,

        'total_meters'         => $total_meters,
        'installed_meters'     => $installed_meters,
        'available_meters'     => $available_meters,
        'faulty_meters'        => $faulty_meters,

        'total_bills'          => $total_bills,
        'paid_bills'           => $paid_bills,
        'unpaid_bills'         => $unpaid_bills,
        'overdue_bills'        => $overdue_bills,

        'total_revenue'        => $total_revenue,
        'payments_today'       => $payments_today,
        'payments_this_month'  => $payments_this_month,
        'pending_payments'     => $pending_payments,

        'payment_methods'      => $payment_methods,
        'recent_payments'      => $recent_payments,
        'recent_bills'         => $recent_bills,
        'total_users'          => $total_users,
        'recent_users'         => $recent_users,
        'recent_customers'     => $recent_customers,

        // NEW
        'pending_approvals'        => $pending_approvals,
        'pending_approval_items'   => $pending_approval_items,

        'monthly_billed' => array_values($monthly_billed),
        'monthly_collected' => array_values($monthly_collected),
        'bill_status_counts' => $bill_status_counts,
    ];
}



private function getOrganizerData(User $user)
{
    // Organizer's events
    $events = Event::where('organizer_id', $user->id)->get();

    // Core statistics
    $totalEvents = $events->count();
    $activeEvents = $events->where('is_active', true)->count();

    // Voting contests
    $totalVotingContests = VotingContest::where('organizer_id', $user->id)->count();
    $activeContests = VotingContest::where('organizer_id', $user->id)
        ->where('is_active', true)
        ->where('end_date', '>', now())
        ->count();

    // Ticket sales and revenue
    $totalTicketSales = TicketPurchase::whereHas('event', function ($query) use ($user) {
        $query->where('organizer_id', $user->id);
    })->where('status', 'paid')->sum('quantity');

    $todaySales = TicketPurchase::whereHas('event', function ($query) use ($user) {
        $query->where('organizer_id', $user->id);
    })->where('status', 'paid')
      ->whereDate('created_at', today())
      ->sum('quantity');

    $totalRevenue = TicketPurchase::whereHas('event', function ($query) use ($user) {
        $query->where('organizer_id', $user->id);
    })->where('status', 'paid')->sum('final_amount');

    $monthRevenue = TicketPurchase::whereHas('event', function ($query) use ($user) {
        $query->where('organizer_id', $user->id);
    })->where('status', 'paid')
      ->whereMonth('created_at', now()->month)
      ->whereYear('created_at', now()->year)
      ->sum('final_amount');

    // Total bookings (polymorphic)
    $totalBookings = Booking::whereHasMorph(
        'bookable',
        [Event::class],
        function ($query) use ($user) {
            $query->where('organizer_id', $user->id);
        }
    )->count();

    // Upcoming events
    $upcomingEvents = Event::where('organizer_id', $user->id)
        ->where('is_active', true)
        ->where('start_date', '>', now())
        ->orderBy('start_date', 'asc')
        ->take(5)
        ->get();

    // Recent bookings
    $recentBookings = Booking::whereHasMorph(
        'bookable',
        [Event::class],
        function ($query) use ($user) {
            $query->where('organizer_id', $user->id);
        }
    )->with(['bookable', 'user'])
     ->latest()
     ->take(5)
     ->get();

    // Recent ticket sales
    $recentTicketSales = TicketPurchase::whereHas('event', function ($query) use ($user) {
        $query->where('organizer_id', $user->id);
    })->with(['event', 'ticket', 'user'])
      ->where('status', 'paid')
      ->latest()
      ->take(5)
      ->get();

    // Performance metrics
    $attendanceRate = $totalEvents > 0 ? min(100, round(($totalBookings / max(1, $totalEvents * 100)) * 100)) : 0;
    $conversionRate = $totalEvents > 0 ? min(100, round(($totalTicketSales / max(1, $totalEvents * 50)) * 100)) : 0;
    $satisfactionRate = 95; // Placeholder until feedback system added

    // Combine into unified stats array
    $stats = [
        'total_events' => $totalEvents,
        'active_events' => $activeEvents,
        'total_voting_contests' => $totalVotingContests,
        'active_contests' => $activeContests,
        'total_ticket_sales' => $totalTicketSales,
        'today_sales' => $todaySales,
        'total_revenue' => $totalRevenue,
        'month_revenue' => $monthRevenue,
        'total_bookings' => $totalBookings,
        'attendance_rate' => $attendanceRate,
        'conversion_rate' => $conversionRate,
        'satisfaction_rate' => $satisfactionRate,
    ];

    // Build recent activity feed dynamically
    $recentActivity = collect();

    foreach ($recentTicketSales as $sale) {
        $recentActivity->push((object)[
            'type' => 'ticket_sale',
            'type_color' => 'green',
            'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z',
            'message' => "New ticket sale for {$sale->event->title}",
            'created_at' => $sale->created_at,
        ]);
    }

    foreach ($recentBookings as $booking) {
        $recentActivity->push((object)[
            'type' => 'booking',
            'type_color' => 'blue',
            'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            'message' => "New booking for {$booking->bookable->title}",
            'created_at' => $booking->created_at,
        ]);
    }

    // Sort and trim feed
    $recentActivity = $recentActivity->sortByDesc('created_at')->take(5);

    // Return the entire dataset
    return [
        'stats' => $stats,
        'upcomingEvents' => $upcomingEvents,
        'recentBookings' => $recentBookings,
        'recentTicketSales' => $recentTicketSales,
        'recentActivity' => $recentActivity,
    ];
}


    private function getVendorData(User $user)
    {
        return [
            'vendor_profile' => $user, // All vendor data
            'total_bookings' => Booking::where('user_id', $user->id)
                ->where('status', 'confirmed')
                ->count(),

            'upcoming_bookings' => Booking::where('user_id', $user->id)
                ->where('status', 'confirmed')
                ->whereHas('bookable', function ($query) {
                    $query->where('start_date', '>=', now());
                })
                ->with('bookable')
                ->latest()
                ->take(5)
                ->get(),

            'total_revenue' => Booking::where('user_id', $user->id)
                ->where('status', 'confirmed')
                ->where('payment_status', 'paid')
                ->sum('amount_paid'),
        ];
    }

    private function getAttendeeData(User $user)
    {
        $userId = $user->id;

        // Paid ticket purchases for the user
        $myTickets = TicketPurchase::where('user_id', $userId)
            ->where('status', 'paid')
            ->with('event')
            ->latest()
            ->get();

        // Votes by the user
        $myVotes = Vote::where('user_id', $userId)
            ->with(['contest', 'nominee'])
            ->latest()
            ->get();

        // Upcoming events the user has tickets for
        $upcomingEvents = TicketPurchase::where('user_id', $userId)
            ->where('status', 'paid')
            ->whereHas('event', function ($query) {
                $query->where('is_active', true)
                    ->where('start_date', '>=', now());
            })
            ->with('event')
            ->latest()
            ->take(5)
            ->get();

        // Total tickets purchased
        $totalTickets = $myTickets->count();

        // Total votes
        $totalVotes = $myVotes->count();

        // Total spent (tickets + votes)
        $totalSpent = $myTickets->sum('final_amount') +
            VotePurchase::where('user_id', $userId)
            ->where('status', 'paid')
            ->sum('amount');

        return [
            'my_tickets'      => $myTickets,
            'my_votes'        => $myVotes,
            'upcoming_tickets' => $upcomingEvents, // rename here
            'total_tickets'   => $totalTickets,
            'total_votes'     => $totalVotes,
            'total_spent'     => $totalSpent,
        ];
    }

    public function notifications()
    {
        $user = Auth::user();
        $notifications = DatabaseNotification::where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('dashboard.notifications', [
            'notifications' => $notifications,

            'title' => 'My Notifications - EventSphere'
        ]);
    }
    public function markNotificationAsRead($id)
    {
        $user = Auth::user();
        $notification = DatabaseNotification::where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', $user->id)
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
        }
    }
    public function markAllNotificationsAsRead()
    {
        $user = Auth::user();
        DatabaseNotification::where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
