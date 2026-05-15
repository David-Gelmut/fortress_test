<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LogController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', []);
    }
}
