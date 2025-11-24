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
use App\Models\Student;
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

            case 'student':
                $data = array_merge($data, $this->getStudentData($user));
                return view('dashboard.student', $data);

            case 'instructor':
                $data = array_merge($data, $this->getInstructorData($user));
                return view('dashboard.instructor', $data);

            case 'receptionist':
                $data = array_merge($data, $this->getReceptionistData($user));
                return view('dashboard.receptionist', $data);

            default: // attendee or fallback
                $data = array_merge($data, $this->getGuestData($user));
                return view('dashboard.guest', $data);
        }
    }

    private function getAdminData()
    {
        // Financial Data
        $total_revenue = Payment::where('payment_status', 'completed')->sum('amount');
        $total_expenses = \App\Models\Expense::where('status', 'approved')->sum('amount');
        $net_profit = $total_revenue - $total_expenses;
        $profit_margin = $total_revenue > 0 ? ($net_profit / $total_revenue) * 100 : 0;

        // Student Data
        $total_students = \App\Models\Student::count();
        $students_with_balance = \App\Models\Student_ef_list::where('balance', '>', 0)->count();
        $outstanding_balance = \App\Models\Student_ef_list::sum('balance');

        // Course Data
        $active_courses = \App\Models\Course::where('status', 1)->count();

        // Payment Data
        $pending_payments = Payment::where('payment_status', 'pending')->count();
        $recent_payments = Payment::with(['EfList.student', 'EfList'])
            ->latest()
            ->take(5)
            ->get();

        // EF Lists Data
        $active_ef_lists = \App\Models\Student_ef_list::where('status', 'active')->count();
        $pending_verifications = \App\Models\Student_ef_list::where('status', 'pending')->count();

        // User Data (excluding students and developers)
        $system_users = \App\Models\User::whereNotIn('role', ['student', 'developer'])

        ->latest()
        ->paginate(10);


        $staff_users = \App\Models\User::whereNotIn('role', ['student', 'developer', 'instructor'])
            ->where('is_active', true)
            ->count();

        $instructors = \App\Models\User::where('role', 'instructor')
            ->where('is_active', true)
            ->count();

        // Expense Data
        $expense_categories = \App\Models\ExpenseCategory::count();

        // Additional Metrics
        $average_payment = Payment::where('payment_status', 'completed')->avg('amount') ?? 0;
        $collection_rate = \App\Models\Student_ef_list::sum('total_fee') > 0 ?
            ((\App\Models\Student_ef_list::sum('paid_amount') / \App\Models\Student_ef_list::sum('total_fee')) * 100) : 0;

        // Chart Data - Monthly Revenue
        $monthly_revenue = Payment::select(
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('payment_status', 'completed')
            ->whereYear('payment_date', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthly_revenue = array_replace(array_fill(1, 12, 0), $monthly_revenue);

        // Chart Data - Monthly Expenses
        $monthly_expenses = \App\Models\Expense::select(
                DB::raw('MONTH(expense_date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('status', 'approved')
            ->whereYear('expense_date', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthly_expenses = array_replace(array_fill(1, 12, 0), $monthly_expenses);

        // Payment Status Distribution
        $payment_status_distribution = Payment::select('payment_status', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_status')
            ->pluck('count', 'payment_status')
            ->toArray();

        // Expense Categories Distribution
        $expense_categories_data = \App\Models\Expense::join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->select('expense_categories.name', DB::raw('SUM(expenses.amount) as total'))
            ->where('expenses.status', 'approved')
            ->groupBy('expense_categories.name')
            ->pluck('total', 'expense_categories.name')
            ->toArray();

        $expense_categories_labels = array_keys($expense_categories_data);
        $expense_categories_data = array_values($expense_categories_data);

        // Course Enrollment
        $course_enrollment = \App\Models\Student_ef_list::join('courses', 'student_ef_lists.course_id', '=', 'courses.id')
            ->select('courses.course', DB::raw('COUNT(*) as count'))
            ->groupBy('courses.course')
            ->pluck('count', 'courses.course')
            ->toArray();

        $course_enrollment_labels = array_keys($course_enrollment);
        $course_enrollment_data = array_values($course_enrollment);

        // Revenue Growth (placeholder - you might want to calculate this properly)
        $revenue_growth = 12.5; // This should be calculated based on previous month comparison

        //spartie permissions
         $permissions_list = \Spatie\Permission\Models\Permission::all();
         $roles = \Spatie\Permission\Models\Role::all();
        return [
            // Financial Metrics
            'total_revenue' => $total_revenue,
            'total_expenses' => $total_expenses,
            'net_profit' => $net_profit,
            'profit_margin' => $profit_margin,
            'revenue_growth' => $revenue_growth,

            // Student Metrics
            'total_students' => $total_students,
            'students_with_balance' => $students_with_balance,
            'outstanding_balance' => $outstanding_balance,

            // Course Metrics
            'active_courses' => $active_courses,

            // Payment Metrics
            'pending_payments' => $pending_payments,
            'recent_payments' => $recent_payments,
            'average_payment' => $average_payment,
            'collection_rate' => $collection_rate,

            // EF List Metrics
            'active_ef_lists' => $active_ef_lists,
            'pending_verifications' => $pending_verifications,

            // User Metrics
            'system_users' => $system_users,
            'staff_users' => $staff_users,
            'instructors' => $instructors,

            // Expense Metrics
            'expense_categories' => $expense_categories,

            // Chart Data
            'monthly_revenue' => array_values($monthly_revenue),
            'monthly_expenses' => array_values($monthly_expenses),
            'payment_status_distribution' => $payment_status_distribution,
            'expense_categories_labels' => $expense_categories_labels,
            'expense_categories_data' => $expense_categories_data,
            'course_enrollment_labels' => $course_enrollment_labels,
            'course_enrollment_data' => $course_enrollment_data,
            //permissions list
            'permissions_list' => $permissions_list,
            'roles'=>$roles,
        ];
    }

    private function getStudentData($user)
    {
        // Get student record
        $student = \App\Models\Student::where('user_id', $user->id)->first();

        if (!$student) {
            return [
                'student' => null,
                'ef_lists' => collect(),
                'total_balance' => 0,
                'total_paid' => 0,
                'recent_payments' => collect(),
                'active_courses' => collect()
            ];
        }

        // Get student's EF lists with course information
        $ef_lists = \App\Models\Student_ef_list::with(['course'])
            ->where('student_id', $student->id)
            ->get();

        // Calculate financial summary
        $total_balance = $ef_lists->sum('balance');
        $total_paid = $ef_lists->sum('paid_amount');
        $total_fee = $ef_lists->sum('total_fee');

        // Get recent payments
        $recent_payments = \App\Models\Payment::with(['efList.course'])
            ->whereHas('efList', function($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get active courses (EF lists with active status)
        $active_courses = $ef_lists->where('status', 'active');

        // Calculate progress for each course
        $courses_with_progress = $ef_lists->map(function($ef_list) {
            $progress = $ef_list->total_fee > 0 ?
                (($ef_list->paid_amount / $ef_list->total_fee) * 100) : 0;

            return [
                'ef_list' => $ef_list,
                'progress' => min($progress, 100),
                'remaining_balance' => $ef_list->balance,
                'payment_status' => $progress >= 100 ? 'Paid' : ($progress > 0 ? 'Partial' : 'Pending')
            ];
        });

        return [
            'student' => $student,
            'ef_lists' => $ef_lists,
            'courses_with_progress' => $courses_with_progress,
            'total_balance' => $total_balance,
            'total_paid' => $total_paid,
            'total_fee' => $total_fee,
            'recent_payments' => $recent_payments,
            'active_courses' => $active_courses,
            'payment_progress' => $total_fee > 0 ? ($total_paid / $total_fee) * 100 : 0
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
