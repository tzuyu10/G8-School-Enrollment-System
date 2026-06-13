<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

Route::get('/', [LoginController::class, 'index'])->name('login');

Route::get('/home', function(){
    return view('common.main');
});

Route::get('/register', function(){
    return view('register');
}); 

Route::fallback(function () {
    return view('404');
});
