<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class UtilController extends Controller
{
    public function redirectToDashboard(Request $request): RedirectResponse
    {
        $role = $request->user()?->role?->code;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'registrar' => redirect()->route('registrar.dashboard'),
            'faculty' => redirect()->route('faculty.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => redirect()->route('unauthorized'),
        };
    }
}
