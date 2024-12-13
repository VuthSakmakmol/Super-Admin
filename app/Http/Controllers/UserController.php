<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        return view('user.index');
    }
    public function profile()
    {
        return view('user.profile'); // Ensure this view exists
    }

    public function settings()
    {
        return view('user.settings'); // Ensure this view exists
    }
}
