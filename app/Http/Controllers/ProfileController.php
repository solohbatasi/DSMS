<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Student;

class ProfileController extends Controller
{
    /**
     * Constructor with middleware
     */


    /**
     * Display the user's profile.
     */
    public function edit()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Load student relationship if user is a student
        if ($user->role === 'student') {
            $user->load('student');
        }

        $viewData = [
            'user' => $user,
            'title' => 'Edit Profile - EventSphere',
            'isStudent' => $user->role === 'student',
            'student' => $user->student ?? null
        ];

        return view('profile.edit', $viewData);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        try {
            // Validate base user data
            $userRules = [
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
                'phone' => 'nullable|string|max:20',
                'bio' => 'nullable|string|max:1000',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ];

            // Add student validation rules if user is a student
            if ($user->role === 'student') {
                $userRules['id_no'] = 'required|string|max:255';
                $userRules['contact'] = 'nullable|string|max:255';
                $userRules['address'] = 'nullable|string|max:255';
            }

            $validatedData = $request->validate($userRules);

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $validatedData['avatar'] = $this->handleAvatarUpload($request->file('avatar'), $user->avatar);
            }

            // Update user
            $user->update($validatedData);

            // Update student data if user is a student
            if ($user->role === 'student' && $user->student) {
                $this->updateStudentData($request, $user->student);
            }

            return redirect()
                ->route('profile.edit')
                ->with('success', 'Profile updated successfully!');

        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Profile update error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'An error occurred while updating your profile. Please try again.');
        }
    }

    /**
     * Handle avatar upload and storage.
     */
    private function handleAvatarUpload($avatarFile, $currentAvatar = null)
    {
        // Delete old avatar if exists
        if ($currentAvatar && Storage::disk('public')->exists($currentAvatar)) {
            Storage::disk('public')->delete($currentAvatar);
        }

        // Store new avatar
        return $avatarFile->store('avatars', 'public');
    }

    /**
     * Update student-specific data.
     */
    private function updateStudentData(Request $request, Student $student)
    {
        $student->update([
            'id_no' => $request->id_no,
            'contact' => $request->contact,
            'address' => $request->address,
        ]);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Soft delete user
        $user->update(['is_active' => false]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('success', 'Your account has been deactivated successfully.');
    }

    /**
     * Display preferences page.
     */
    public function preferences()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return view('profile.preferences', [
            'user' => $user,
            'preferences' => $user->preferences ?? [
                'email_notifications' => true,
                'sms_notifications' => false,
                'newsletter' => true,
                'event_recommendations' => true,
                'voting_updates' => true,
            ],
            'title' => 'Notification Preferences - EventSphere'
        ]);
    }

    /**
     * Update user preferences.
     */
    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'sometimes|boolean',
            'sms_notifications' => 'sometimes|boolean',
            'newsletter' => 'sometimes|boolean',
            'event_recommendations' => 'sometimes|boolean',
            'voting_updates' => 'sometimes|boolean',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $currentPreferences = $user->preferences ?? [];
        $updatedPreferences = array_merge($currentPreferences, $validated);

        $user->update(['preferences' => $updatedPreferences]);

        return back()->with('success', 'Preferences updated successfully!');
    }

    /**
     * Display security settings page.
     */
    public function security()
    {
        return view('profile.security', [
            'title' => 'Security Settings - EventSphere'
        ]);
    }

    /**
     * Display user activity page.
     */
    public function activity()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Note: You'll need to adjust these based on your actual relationships
        $activities = [
            'ticket_purchases' => [],
            'votes' => [],
        ];

        return view('profile.activity', [
            'user' => $user,
            'activities' => $activities,
            'title' => 'My Activity - EventSphere'
        ]);
    }
}
