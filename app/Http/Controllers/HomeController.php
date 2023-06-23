<?php

namespace App\Http\Controllers;

use App\Meta;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{



    public function index(): Response
    {
        $defaultPayment = auth()->user()->defaultPaymentMethod();
        return Inertia::render('AiPhoto/Home', [
            "is_beginner" => !isset($defaultPayment)
        ]);
    }

    public function landing(): Response
    {
        Meta::addMeta('title', env('APP_NAME') . ' - AI Headshots!');

        return Inertia::render('Welcome');
    }

    public function enterprise(Request $request)
    {
        $enterprises = Employee::where('email', auth()->user()->email)->with('enterprise')->paginate(10);

        return Inertia::render('Enterprise/Enterprise', [
            'enterprises' => $enterprises
        ]);
    }

    public function invite(Request $request)
    {
        $token = $request->token;
        $enterprise = Employee::where('status', 0)->where('token', $token)->where('email', auth()->user()->email)->with('enterprise')->first();
        // $enterprise = Employee
        return Inertia::render('Enterprise/Invite', ['enterprise' => $enterprise]);
    }
    public function accept(Request $request)
    {
        $token = $request->token;
        $enterprise = Employee::where('status', 0)->where('token', $token)->where('email', auth()->user()->email)->with('enterprise')->first();
        if(isset($enterprise)) {
            $enterprise->status = 1;
            $enterprise->token = null;
            $enterprise->save();
            return redirect()->route('user.enterprises')->with('message', __('res.Accepted'));
        } 
        
        return redirect()->route('user.enterprises')->with('message', __('res.Something'));
    }
}