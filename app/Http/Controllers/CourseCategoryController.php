<?php

namespace App\Http\Controllers;

use App\Models\CourseCategory;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Base query with branch filtering
        $categoriesQuery = CourseCategory::with(['branch', 'courses']);

        // Apply branch filtering
        if ($user->isAdmin()) {
            $selectedBranchId = session('admin_selected_branch');
            if ($selectedBranchId) {
                $categoriesQuery->where('branch_id', $selectedBranchId);
            }
        } else {
            $categoriesQuery->where('branch_id', $user->branch_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $categoriesQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $categoriesQuery->orderBy('name')->paginate(10);

        // Get branches for admin filter
        $branches = $user->isAdmin() ? Branch::active()->get() : collect();

        return view('course-categories.index', [
            'categories' => $categories,
            'branches' => $branches,
            'selectedBranch' => session('admin_selected_branch')
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:course_categories,name',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
            'branch_id' => $user->isAdmin() ? 'required|exists:branches,id' : 'nullable'
        ]);

        // Set branch for non-admin users
        if (!$user->isAdmin()) {
            $validated['branch_id'] = $user->branch_id;
        } else {
            // Admin can use selected branch if not specified
            $selectedBranchId = session('admin_selected_branch');
            if ($selectedBranchId && !$request->has('branch_id')) {
                $validated['branch_id'] = $selectedBranchId;
            }
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('category-images', 'public');
            $validated['image'] = $imagePath;
        }

        $category = CourseCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'category' => $category
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $category = CourseCategory::findOrFail($id);

        // Check permission
        if (!$user->isAdmin() && $category->branch_id != $user->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this category.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => "required|string|max:255|unique:course_categories,name,{$id}",
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
            'branch_id' => $user->isAdmin() ? 'required|exists:branches,id' : 'nullable'
        ]);

        // Non-admin users cannot change branch
        if (!$user->isAdmin()) {
            unset($validated['branch_id']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image) {
                \Storage::disk('public')->delete($category->image);
            }
            $imagePath = $request->file('image')->store('category-images', 'public');
            $validated['image'] = $imagePath;
        }

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'category' => $category
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $category = CourseCategory::findOrFail($id);

        // Check permission
        if (!$user->isAdmin() && $category->branch_id != $user->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this category.'
            ], 403);
        }

        // Check if category has courses
        if ($category->courses()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category that has courses. Please delete or reassign courses first.'
            ], 422);
        }

        // Delete image if exists
        if ($category->image) {
            \Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.'
        ]);
    }

    // Get categories for dropdown (for course creation)
    public function getCategories(Request $request)
    {
        $user = Auth::user();

        $query = CourseCategory::where('status', 1)
            ->orderBy('name');

        // Apply branch filtering
        if ($user->isAdmin()) {
            $selectedBranchId = session('admin_selected_branch');
            if ($selectedBranchId) {
                $query->where('branch_id', $selectedBranchId);
            }
        } else {
            $query->where('branch_id', $user->branch_id);
        }

        $categories = $query->get();

        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }
}
