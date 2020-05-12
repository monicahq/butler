<?php

namespace App\Http\Controllers\Settings;

use Inertia\Inertia;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\ViewHelpers\Settings\SettingsControllerViewHelper;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     *
     * @return Response
     */
    public function index()
    {
        $account = auth()->user()->account;

        $templates = SettingsControllerViewHelper::templates($account);
        $attributes = SettingsControllerViewHelper::attributes($account);

        return Inertia::render('Settings/Index', [
            'templates' => $templates,
            'attributes' => $attributes,
        ]);
    }
}
