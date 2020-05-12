<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\CreateAccount;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Foundation\Auth\ThrottlesLogins;

class RegisterController extends Controller
{
    use ThrottlesLogins;

    /**
     * Show the register page.
     *
     * @return Response
     */
    public function index()
    {
        if (Auth::check()) {
            return redirect('/home');
        }

        return Inertia::render('Auth/Register');
    }

    /**
     * Store the user.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        (new CreateAccount)->execute([
            'first_name' => $request->input('firstname'),
            'last_name' => $request->input('lastname'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        Auth::attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        return Redirect::route('home');
    }
}
