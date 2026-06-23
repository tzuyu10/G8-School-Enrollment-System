<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Profile;
use App\Models\ProfileStatus;
use App\Models\Role;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'The provided credentials do not match our records.'])
                ->onlyInput('email');
        }

        $user = Auth::user()->load(['role', 'status']);

        if ($user->status?->code === 'inactive') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('account.inactive');
        }

        $request->session()->regenerate();
        return app(UtilController::class)->redirectToDashboard($request);
    }

    public function register(RegisterRequest $request)
    {
        $studentRole  = Role::where('code', 'student')->firstOrFail();
        $activeStatus = ProfileStatus::where('code', 'active')->firstOrFail();

        $profile = Profile::create([
            'id'          => (string) Str::uuid(),
            'role_id'     => $studentRole->id,
            'status_id'   => $activeStatus->id,
            'first_name'  => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name'   => $request->last_name,
            'suffix'      => $request->suffix,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
        ]);

        StudentProfile::create([
            'profile_id'           => $profile->id,
            'student_type'         => 'freshman',
            'birthdate'            => $request->birthdate,
            'gender'               => $request->gender,
            'civil_status'         => $request->civil_status,
            'nationality'          => $request->nationality ?? 'Filipino',
            'religion'             => $request->religion,
            'contact_number'       => $request->contact_number,
            'permanent_address'    => $request->permanent_address,
            'current_address'      => $request->current_address,
            'father_first_name'    => $request->father_first_name,
            'father_middle_name'   => $request->father_middle_name,
            'father_last_name'     => $request->father_last_name,
            'father_suffix'        => $request->father_suffix,
            'mother_first_name'    => $request->mother_first_name,
            'mother_middle_name'   => $request->mother_middle_name,
            'mother_last_name'     => $request->mother_last_name,
            'mother_suffix'        => $request->mother_suffix,
            'guardian_first_name'  => $request->guardian_first_name,
            'guardian_middle_name' => $request->guardian_middle_name,
            'guardian_last_name'   => $request->guardian_last_name,
            'guardian_suffix'      => $request->guardian_suffix,
            'guardian_relation'    => $request->guardian_relation,
            'guardian_contact'     => $request->guardian_contact,
            'previous_school'      => $request->previous_school,
            'previous_program'     => $request->previous_program,
        ]);

        return redirect()
            ->route('login')
            ->with('status', 'Registration successful! You can now log in.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}