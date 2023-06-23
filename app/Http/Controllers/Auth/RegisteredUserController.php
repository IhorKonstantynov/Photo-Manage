<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\Employee;
use App\Models\Plan;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
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
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validate = [            
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'account_type' => ['required', Rule::in(['0', '1'])],
        ];

        if($request->account_type == 1) {
            $validate['company_name'] = 'required|string|max:255';
        }

        $request->validate($validate);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'utm_source' => $request->utm_source,
            'utm_medium' => $request->utm_medium,
            'utm_campaign' => $request->utm_campaign,
            'utm_content' => $request->utm_content,
            'account_type' => $request->account_type,
            'company_name' => $request->company_name
        ]);

        event(new Registered($user));

        Auth::login($user);
        $authUser = auth()->user();
        $employee = Employee::where('email', $authUser->email)->first();
        if(isset($employee)) {
            $authUser->available_employee = -1;
            $authUser->save();
        }
        Mail::to($authUser)->send(new WelcomeMail($authUser));
        // return redirect()->intended($this->redirectPath());
        return redirect(RouteServiceProvider::HOME);
    }
}