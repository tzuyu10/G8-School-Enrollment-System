<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistrarController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UtilController;

// ─── Guest only (redirect authenticated users to their dashboard) ─
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'index'])->name('login');
    Route::get('/register', function () {
        return view('register');
    })->name('register');
});

// ─── Public POST routes ───────────────────────────────────────────
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/register', [LoginController::class, 'register'])->name('register.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ─── Status pages (public) ────────────────────────────────────────
Route::view('/unauthorized', 'errors.unauthorized')->name('unauthorized');
Route::view('/account/pending', 'account.pending')->name('account.pending');
Route::view('/account/inactive', 'account.inactive')->name('account.inactive');

// ─── Authenticated routes ─────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Student
    Route::middleware('role:student')->group(function () {
        Route::get('/student', [StudentController::class, 'index'])->name('student.dashboard');
        Route::get('/enroll', [EnrollmentController::class, 'form'])->name('enroll.form');
        Route::post('/enroll', [EnrollmentController::class, 'submit'])->name('enroll.submit');
    });

    // Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::put('/admin/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    });

    // Registrar
    Route::middleware('role:registrar')->group(function () {
        Route::get('/registrar', [RegistrarController::class, 'index'])->name('registrar.dashboard');
        Route::put('/registrar/applications/{id}/approve', [RegistrarController::class, 'approve'])->name('registrar.approve');
        Route::put('/registrar/applications/{id}/reject', [RegistrarController::class, 'reject'])->name('registrar.reject');
    });

    // Faculty
    Route::middleware('role:faculty')->group(function () {
        Route::get('/faculty', [FacultyController::class, 'index'])->name('faculty.dashboard');
    });

});

// ─── Fallback ─────────────────────────────────────────────────────
Route::fallback(function () {
    return view('404');
});