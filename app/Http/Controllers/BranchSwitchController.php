<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;

class BranchSwitchController extends Controller
{


    public function select()
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'Only administrators can select branches.');
        }

        $branches = Branch::active()->get();
        return view('branches.select', compact('branches'));
    }

    public function switch(Request $request)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'Only administrators can switch branches.');
        }

        $request->validate([
            'branch_id' => 'required|exists:branches,id'
        ]);

        // Store selected branch in session
        session(['admin_selected_branch' => $request->branch_id]);

        // Also store the branch name for display
        $branch = Branch::find($request->branch_id);
        session(['admin_selected_branch_name' => $branch->name]);

        return redirect()->route('dashboard')
            ->with('success', "Now viewing data for {$branch->name} branch.");
    }

    public function clear()
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'Only administrators can clear branch selection.');
        }

        session()->forget('admin_selected_branch');
        session()->forget('admin_selected_branch_name');

        return redirect()->route('dashboard')
            ->with('success', 'Now viewing all branches.');
    }

    public function updateSelected(Request $request)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only administrators can switch branches.'
            ], 403);
        }

        if ($request->branch_id) {
            $branch = Branch::find($request->branch_id);
            if (!$branch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Branch not found.'
                ], 404);
            }

            session(['admin_selected_branch' => $request->branch_id]);
            session(['admin_selected_branch_name' => $branch->name]);

            return response()->json([
                'success' => true,
                'message' => 'Branch switched successfully.',
                'branch_name' => $branch->name,
                'branch_id' => $branch->id
            ]);
        } else {
            // Clear selection (view all branches)
            session()->forget('admin_selected_branch');
            session()->forget('admin_selected_branch_name');

            return response()->json([
                'success' => true,
                'message' => 'Now viewing all branches.'
            ]);
        }
    }
}
