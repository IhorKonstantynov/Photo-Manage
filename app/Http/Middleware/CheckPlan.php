<?php

namespace App\Http\Middleware;

use App\Models\Employee;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $is): Response
    {
        if($is == 'home') {
            if (!auth()->user()->checkPlan()) {
                $enterprise = Employee::where([
                    'email' => auth()->user()->email 
                ])->where('credit', '>', 0)->first();
                if(isset($enterprise)) {
                    return redirect()->route('user.enterprises')->with('info', 'You are not authorized to access this page.');
                }
                return redirect()->route('profile.plans')->with('info', 'You are not authorized to access this page.');
            }
        } else if ($is == 'enterprise') {
            $en_id = $request->en_id;
            $enterprise = Employee::where([
                'id' => $en_id,
                'email' => auth()->user()->email,
                'status' => 1, 
            ])->where('credit', '>', 0)->first();
            if(!isset($enterprise)) {
                return redirect()->route('user.enterprises')->with('info', 'You are not authorized to access this page.');
            }
            // if (!auth()->user()->checkPlan()) {
            //     return redirect()->route('profile.plans')->with('info', 'You are not authorized to access this page.');
            // }
        } else {
            if (auth()->user()->checkPlan()) {
                return redirect()->route('home')->with('info', 'You are not authorized to access this page.');
            }
        }

        return $next($request);
    }
}
