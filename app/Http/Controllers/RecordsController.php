<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RecordsController extends Controller
{
    public function records(Request $request)
    {
        return view('student.records');
    }
}
