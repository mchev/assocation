<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\HelloAssoOrganizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class HelloassoController extends Controller
{
    public function __construct(
        protected HelloAssoOrganizationService $helloAssoOrganizationService
    ) {}

    /**
     * Redirect the user to the HelloAsso authentication page.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('helloasso')->redirect();
    }

    /**
     * Obtain the user information from HelloAsso.
     */
    public function callback(): RedirectResponse
    {
        try {
            $helloassoUser = Socialite::driver('helloasso')->user();

            DB::beginTransaction();

            $user = User::updateOrCreate([
                'email' => $helloassoUser->email,
            ], [
                'name' => $helloassoUser->name,
                'helloasso_id' => $helloassoUser->id,
                'helloasso_token' => $helloassoUser->token,
                'helloasso_refresh_token' => $helloassoUser->refreshToken,
                'avatar_path' => $helloassoUser->avatar,
            ]);

            // Synchroniser les organisations HelloAsso
            $this->helloAssoOrganizationService->syncOrganizations(
                $user,
                $helloassoUser->organizations ?? []
            );

            DB::commit();

            Auth::login($user);

            return redirect()->intended(route('app.dashboard'));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('login')->with('error', 'Une erreur est survenue lors de la connexion avec HelloAsso.');
        }
    }
}
