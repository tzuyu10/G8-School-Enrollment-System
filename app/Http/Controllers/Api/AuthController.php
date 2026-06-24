<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Profile;
use App\Models\ProfileStatus;
use App\Models\Role;
use App\Models\StudentProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        // Get student role and pending status
        $studentRole   = Role::where('code', 'student')->firstOrFail();
        $pendingStatus = ProfileStatus::where('code', 'pending')->firstOrFail();

        // Create profile
        $profile = Profile::create([
            'id'        => \Illuminate\Support\Str::uuid(),
            'role_id'   => $studentRole->id,
            'status_id' => $pendingStatus->id,
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
        ]);

        // Create student profile with enrollment form data
        StudentProfile::create([
            'profile_id'        => $profile->id,
            'student_type'      => $request->student_type,
            'birthdate'         => $request->birthdate,
            'gender'            => $request->gender,
            'civil_status'      => $request->civil_status,
            'nationality'       => $request->nationality ?? 'Filipino',
            'religion'          => $request->religion,
            'contact_number'    => $request->contact_number,
            'permanent_address' => $request->permanent_address,
            'current_address'   => $request->current_address,
            'guardian_name'     => $request->guardian_name,
            'guardian_relation' => $request->guardian_relation,
            'guardian_contact'  => $request->guardian_contact,
            'father_name'       => $request->father_name,
            'mother_name'       => $request->mother_name,
            'previous_school'   => $request->previous_school,
            'previous_program'  => $request->previous_program,
        ]);

        $token = $profile->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful. Your application is pending approval.',
            'token'   => $token,
            'user'    => [
                'id'        => $profile->id,
                'full_name' => $profile->full_name,
                'email'     => $profile->email,
                'role'      => $profile->role->code,
                'status'    => $profile->status->code,
            ],
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $profile = Profile::with(['role', 'status'])
            ->where('email', $request->email)
            ->first();

        if (!$profile || !Hash::check($request->password, $profile->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        if ($profile->status?->code === 'inactive') {
            return response()->json([
                'message' => 'Account inactive.',
            ], 403);
        }

        // Revoke previous tokens
        $profile->tokens()->delete();

        $token = $profile->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'token'   => $token,
            'user'    => [
                'id'        => $profile->id,
                'full_name' => $profile->full_name,
                'email'     => $profile->email,
                'role'      => $profile->role->code,
                'status'    => $profile->status->code,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $profile = $request->user()->load(['role', 'status', 'studentProfile']);

        return response()->json([
            'user' => $profile,
        ]);
    }
}
