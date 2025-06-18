<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {

            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate([
                'email' => $googleUser->email,
            ], [
                'name' => $googleUser->name,
                'google_id' => $googleUser->id,
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'google_avatar' => $googleUser->avatar,
                'google_avatar_url' => $googleUser->avatar_url,
            ]);

            Auth::login($user);

            return redirect()->intended(route('home'));

        } catch (\Exception $e) {
            Log::error('Google authentication error: '.$e->getMessage());

            return redirect()->route('login')->with('error', 'Une erreur est survenue lors de la connexion avec Google.');
        }
    }
}
