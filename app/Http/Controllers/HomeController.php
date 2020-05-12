<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    /**
     * Display the user home page.
     *
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Dashboard/Index');
    }
}
