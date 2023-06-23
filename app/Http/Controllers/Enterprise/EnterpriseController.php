<?php

namespace App\Http\Controllers\Enterprise;

use App\Http\Controllers\Controller;
use App\Mail\EnterpriseInviteMail;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class EnterpriseController extends Controller
{
    public function index(Request $request)
    {
        $enterprise_id = auth()->user()->id;
        $employees = Employee::where('enterprise_id', $enterprise_id)->paginate(10);

        return Inertia::render('Enterprise/Employees', compact('employees'));
    }

    public function invite(Request $request)
    {
        $enterprise_id = auth()->user()->id;
        $emails = $request->emails;
        foreach ($emails as $email) {
            if(empty($email)) continue;
            $employee = Employee::where('email', $email)->where('enterprise_id', $enterprise_id)->first();
            if(!isset($employee)) {
                $token = Str::random(10);
                $employee = Employee::create([
                    'enterprise_id' => $enterprise_id,
                    'email' => $email,
                    'token' => $token,
                    'credit' => 1
                ]);
                auth()->user()->available_employee -= 1;
                $price = 25;
                if(auth()->user()->isDiscount()) {
                    $price *= 0.8;
                }
                auth()->user()->spent += $price;
                $user = User::where('email', $email)->first();
                if(isset($user)) {
                    $user->available_employee = -1;
                    $user->save();
                }
                Mail::to($email)->send(new EnterpriseInviteMail(auth()->user(), $token, isset($user)));
            }
        }
        auth()->user()->save();
        $employees = Employee::where('enterprise_id', $enterprise_id)->paginate(10);
        return redirect()->route('enterprise.employees', $request->query())->with('employees', $employees);
    }
}