<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function home()
    {
        Auth::user()->can('rh') ?: abort(403, 'SEM AUTORIZAÇÃO');
        return view('home');
    }
}
