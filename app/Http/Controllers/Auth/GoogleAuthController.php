<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request)
    {
        // Stash the page the user was on before we send them off to Google —
        // this round trip loses everything except what we deliberately persist,
        // and the session is the one thing that survives it.
        if (! $request->session()->has('post_login_redirect')) {
            $intended = $request->session()->get('url.intended');
            $currentUrl = $request->query('current_url');

            $target = $intended ?: $currentUrl;

            if ($target && str_starts_with($target, config('app.url'))) {
                $request->session()->put('post_login_redirect', $target);
            }
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            if (! $user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
        } else {
            $user = User::create([
                'name' => $googleUser->getName(),
                'username' => $this->generateUniqueUsername($googleUser->getName()),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => null,
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user, remember: true);
        $user->update(['is_online' => true, 'last_login_at' => now()]);

        // New Google users won't have programme_id/level_id set yet —
        // route them to complete their profile instead of straight to dashboard.


        $redirectUrl = $request->session()->pull('post_login_redirect');

        if ($redirectUrl) {
            return redirect()->to($redirectUrl);
        }

        if (! $user->programme_id) {
            return redirect()->route('setup.store');
        }


        return redirect()->route('home.index');
    }

    private function generateUniqueUsername(string $name): string
    {
        $base = Str::slug($name, '_') ?: 'user';
        $username = $base;
        $suffix = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . '_' . $suffix++;
        }

        return $username;
    }
}
