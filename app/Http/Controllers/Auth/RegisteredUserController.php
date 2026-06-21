<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
      $validatedData =   $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'currentUrl' => [
                'nullable',
                'string',
                'max:255',
            ],

            'username' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('users', 'username'),
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'phone'),
            ],

            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],
        ]);

        $user = User::create([
            'name' => trim($validatedData['name']),
            'username' => strtolower(trim($validatedData['username'])),
            'email' => strtolower(trim($validatedData['email'])),
            'phone' => $request->phone ? trim($validatedData['phone']) : null,
            'is_online' => true,
            'last_login_at' => Carbon::now(),
            'password' => Hash::make($validatedData['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($request->currentUrl && str_starts_with($request->currentUrl, config('app.url'))) {
            return redirect()->to($request->currentUrl);
        }
        return redirect()->intended(route('dashboard', absolute: false));
    }
}
