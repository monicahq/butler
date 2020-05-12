<?php

namespace App\Http\Controllers\Settings;

use Inertia\Inertia;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class AttributesController extends Controller
{
    /**
     * Display the settings page.
     *
     * @return Response
     */
    public function create()
    {
        return Inertia::render('Settings/Attribute/Create', []);
    }
}
