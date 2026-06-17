<?php

namespace App\Http\Controllers;

use App\Models\EnrollmentApplication;
use App\Models\Profile;
use App\Models\ProfileStatus;
use App\Models\Role;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.admin', [
            'profileCount' => Profile::count(),
            'pendingApplications' => EnrollmentApplication::whereHas('status', fn ($query) => $query->where('code', 'pending'))->count(),
            'recentApplications' => EnrollmentApplication::with(['student', 'program', 'status'])
                ->orderBy('submitted_at', 'desc')
                ->limit(5)
                ->get(),
        ]);
    }

    public function users(Request $request)
    {
        $query = Profile::with(['role', 'status', 'studentProfile']);

        if ($request->filled('role')) {
            $query->whereHas('role', fn ($role) => $role->where('code', $request->role));
        }

        if ($request->filled('status')) {
            $query->whereHas('status', fn ($status) => $status->where('code', $request->status));
        }

        return view('admin.admin-users', [
            'users' => $query->orderBy('created_at', 'desc')->get(),
            'roles' => Role::orderBy('label')->get(),
            'statuses' => ProfileStatus::orderBy('label')->get(),
            'filters' => $request->only(['role', 'status']),
        ]);
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'unique:profiles,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:faculty,registrar,admin'],
        ]);

        $role = Role::where('code', $validated['role'])->firstOrFail();
        $activeStatus = ProfileStatus::where('code', 'active')->firstOrFail();

        Profile::create([
            'id' => (string) Str::uuid(),
            'role_id' => $role->id,
            'status_id' => $activeStatus->id,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'suffix' => $validated['suffix'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users')->with('status', 'User account created successfully.');
    }

    public function updateUser(Request $request, string $id)
    {
        $validated = $request->validate([
            'role' => ['required', 'exists:roles,code'],
            'status' => ['required', 'exists:profile_statuses,code'],
        ]);

        $user = Profile::findOrFail($id);

        $user->update([
            'role_id' => Role::where('code', $validated['role'])->firstOrFail()->id,
            'status_id' => ProfileStatus::where('code', $validated['status'])->firstOrFail()->id,
        ]);

        return redirect()->route('admin.users')->with('status', 'User account updated successfully.');
    }

    public function activateAllSemesters()
    {
        Semester::query()->update(['is_active' => true]);

        return redirect()->route('admin.dashboard')
            ->with('status', 'All semesters are now active for demo use.');
    }
}
