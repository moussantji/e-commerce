<?php

namespace App\Http\Controllers\CustomerPanel;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CustomerComtroller extends Controller
{
    public function dashboard()
    {
        if(Auth::user()->role === 'admin')
        {
            return redirect()->route('admin.dashboard');
        }
        return view('dashboard');
    }
}
