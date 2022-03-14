<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;

class HomeController extends Controller
{
    public function home(): Application|Factory|\Illuminate\Contracts\View\View
    {
        return view('home')->with([
            'bundles' => Bundle::all(),
        ]);
    }
}
