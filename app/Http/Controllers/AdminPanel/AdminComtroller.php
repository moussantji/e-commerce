<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminComtroller extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }
}
