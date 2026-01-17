<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBranchAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admin has access to everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check if the route/model belongs to user's branch
        $branchId = $this->getBranchIdFromRequest($request);

        if ($branchId && !$user->canAccessBranch($branchId)) {
            abort(403, 'You do not have permission to access this branch data.');
        }

        return $next($request);
    }

    private function getBranchIdFromRequest(Request $request)
    {
        // Check route parameters
        if ($request->route('branch')) {
            return $request->route('branch');
        }

        if ($request->route('student')) {
            $student = \App\Models\Student::find($request->route('student'));
            return $student?->branch_id;
        }

        if ($request->route('course')) {
            $course = \App\Models\Course::find($request->route('course'));
            return $course?->branch_id;
        }

        // Check for branch_id in request for create/update
        if ($request->has('branch_id')) {
            return $request->branch_id;
        }

        return null;
    }
}
