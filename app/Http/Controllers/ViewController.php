<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ViewController extends Controller
{
    public function welcome(): View
    {
        return view('welcome');
    }

    public function vote(): View
    {
        return view('vote');
    }

    public function stat(): View
    {
        return view('stat');
    }
}
