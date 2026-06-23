<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\ProfileStatus;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // Admin: list all users
    public function index(Request $request): JsonResponse
    {
        $query = Profile::with(['role', 'status', 'studentProfile']);

        if ($request->has('role')) {
            $query->whereHas('role', fn($q) => $q->where('code', $request->role));
        }

        if ($request->has('status')) {
            $query->whereHas('status', fn($q) => $q->where('code', $request->status));
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'users' => $users,
        ]);
    }

    // Admin: create faculty/registrar/admin account
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:profiles,email'],
            'password'  => ['required', 'string', 'min:8'],
            'role'      => ['required', 'in:faculty,registrar,admin'],
        ]);

        $role          = Role::where('code', $request->role)->firstOrFail();
        $activeStatus  = ProfileStatus::where('code', 'active')->firstOrFail();

        $user = Profile::create([
            'id'        => Str::uuid(),
            'role_id'   => $role->id,
            'status_id' => $activeStatus->id,
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User created successfully.',
            'user'    => $user->load(['role', 'status']),
        ], 201);
    }

    // Admin: update user role or status
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'full_name' => ['sometimes', 'string', 'max:255'],
            'role'      => ['sometimes', 'in:student,faculty,registrar,admin'],
            'status'    => ['sometimes', 'in:pending,active,inactive'],
            'password'  => ['sometimes', 'string', 'min:8'],
        ]);

        $user = Profile::findOrFail($id);

        if ($request->has('role')) {
            $user->role_id = Role::where('code', $request->role)->firstOrFail()->id;
        }

        if ($request->has('status')) {
            $user->status_id = ProfileStatus::where('code', $request->status)->firstOrFail()->id;
        }

        if ($request->has('full_name')) {
            $user->full_name = $request->full_name;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'User updated successfully.',
            'user'    => $user->fresh()->load(['role', 'status']),
        ]);
    }
}