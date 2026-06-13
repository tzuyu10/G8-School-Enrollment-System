<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/register', function(){
    return view('register');
}); 

Route::fallback(function () {
    return view('404');
});
