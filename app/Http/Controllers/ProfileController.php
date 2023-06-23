<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Update ther user's promotion code for discount.
     */
    public function updatePromo(Request $request): RedirectResponse
    {
        $user = $request->user();

        $promo_code = $request->promo_code;

        if(isset($promo_code) && !empty($promo_code) && $promo_code == env('PROMO_CODE', 'FRIENDS20')) {
            $user->promo_code = $promo_code;
            $user->save();
        } else {
            return Redirect::route('profile.edit')->withErrors(['promo_code' => __('res.InvalidPromo')]);
        }
        return Redirect::route('profile.edit')->with('status', 'promo-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $subscription = $user->subscription(env('STRIPE_NAME'));
        if(isset($subscription)) {
            $subscription->cancel();
            $subscription->items->first()->delete();
            $subscription->delete();
        };
        $user->deletePaymentMethods();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
