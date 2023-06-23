<?php

namespace App\Http\Controllers;

use App\Mail\SendPromoMail;
use App\Models\Photos;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $id = $request->id ?? "";
        $email = $request->email ?? "";
        $name = $request->name ?? "";
        $account_type = $request->account_type ?? 2;
        $utm_source = $request->utm_source ?? "";
        $utm_campaign = $request->utm_campaign ?? "";

        $users = User::with('enterprise')->with('plan')->with('photos')->with('photoEdit');

        if (!empty($id)) {
            $users->where('id', 'like', "%$id%");
        }
        if (!empty($email)) {
            $users->where('email', 'like', "%$email%");
        }
        if (!empty($name)) {
            $users->where('name', 'like', "%$name%");
        }
        if ($account_type != 2) {
            $users->where('account_type', $account_type);
        }
        if (!empty($utm_source)) {
            $users->where('utm_source', 'like', "%$utm_source%");
        }
        if (!empty($utm_campaign)) {
            $users->where('utm_campaign', 'like', "%$utm_campaign%");
        }
        $users = $users->paginate(10);
        $users = $users->toArray();
        foreach ($users['data'] as $key => $user) {
            $totalPay = 0;
            $pendingPay = 0;


            $total1 = User::with('photos.plan')->where('id', '!=', $user['id'])->where('utm_content', $user['id'])->where('utm_source', str_replace(' ', '', $user['name']))->whereNull(['utm_medium', 'utm_campaign'])->where('id', '>', '2151')->get();
            $total2 = User::with('photos.plan')->where('id', '!=', $user['id'])->where('utm_content', $user['id'])->where('utm_source', '!=', 'enterprise')->where('id', '<', '2151')->get();

            $total = array_merge($total1->toArray(), $total2->toArray());
            foreach ($total as $item) {
                foreach ($item['photos'] as $photo) {
                    $totalPay += $photo['plan']['price'];
                    if ($photo['status'] == 2) {
                        $pendingPay += $photo['plan']['price'] * 0.2;
                    }
                }
            }

            $user['totalPay'] = $totalPay;
            $user['pendingPay'] = $pendingPay;
            $users['data'][$key] = $user;
        }
        $plans = Plan::get();
        return Inertia::render('Admin/Users', [
            'users' => $users,
            'plans' => $plans,
            'csrfToken' => csrf_token(),
            'search' => [
                'id' => $id,
                'email' => $email,
                'name' => $name,
                'account_type' => $account_type,
                'utm_source' => $utm_source,
                'utm_campaign' => $utm_campaign,
            ],
            'message' => session('message'),
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $plan_id = $request->plan_id ?? 1;
        $plan = Plan::find($plan_id);
        $user = User::find($id);
        $user->plan_id = $plan->id;
        $user->isFree = 1;
        $user->save();

        return response()->json(['message' => 'successfull']);
    }

    public function sendPromo(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);
        Mail::to($user)->send(new SendPromoMail($user));
        $user->received_email = 1;
        $user->save();
        return response()->json(['message' => 'successfull']);
    }
    public function review_payment_request(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);
        if (isset($user)) {
            $totalSale = 0;
            $totalPay = 0;
            $pendingPay = 0;
            $earningPay = $user->ref_earn;
            $currentPay = 0;
            $refundedPay = 0;

            $total1 = User::with('photos.plan')->where('id', '!=', $user->id)->where('utm_source', str_replace(' ', '', $user->name))->whereNull(['utm_medium', 'utm_campaign'])->where('utm_content', $user->id)->where('id', '>', '2151')->get();
            $total2 = User::with('photos.plan')->where('id', '!=', $user->id)->where('utm_source', '!=', 'enterprise')->where('utm_content', $user->id)->where('id', '<', '2151')->get();

            $total = array_merge($total1->toArray(), $total2->toArray());
            $totalCount = count($total);
            foreach ($total as $item) {
                foreach ($item['photos'] as $photo) {
                    if($photo['isFree'] == 1) continue;
                    $totalSale++;
                    $totalPay += $photo['plan']['price'];
                    if ($photo['status'] == 1) {
                        $currentPay += $photo['plan']['price'] * 0.2;
                    } else if ($photo['status'] == 2) {
                        $pendingPay += $photo['plan']['price'] * 0.2;
                    } else if ($photo['status'] == 3) {
                        // $earningPay += $photo['plan']['price'] * 0.2;
                    } else if ($photo['status'] == 4 || $photo['status'] == 5 || $photo['status'] == 6) {
                        $refundedPay += $photo['plan']['price'];
                    }
                    if ($photo['status'] == 5) {
                        $currentPay -= $photo['plan']['price'] * 0.2;
                    }
                    if ($photo['status'] == 6) {
                        $pendingPay -= $photo['plan']['price'] * 0.2;
                    }
                }
            }

            return Inertia::render('Referrals/Dashboard', compact('totalCount', 'totalSale', 'totalPay', 'user', 'pendingPay', 'earningPay', 'refundedPay', 'currentPay'));
        }
    }

    public function confirm_payment_request(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);
        $user_Ids1 = User::where('id', '!=', $user->id)->where('utm_source', str_replace(' ', '', $user->name))->whereNull(['utm_medium', 'utm_campaign'])->where('utm_content', $user->id)->where('id', '>', '2151')->pluck('id');
        $user_Ids2 = User::where('id', '!=', $user->id)->where('utm_source', '!=', 'enterprise')->where('utm_content', $user->id)->where('id', '<', '2151')->pluck('id');
        $user_Ids = array_merge($user_Ids1->toArray(), $user_Ids2->toArray());
        $pendingPay = Photos::leftJoin('plans', 'plans.id', 'photos.plan_id')->whereIn('photos.user_id', $user_Ids)->where('isFree', 0)->where('photos.status', 2)->sum('plans.price') * 0.2;
        $refundedPay = Photos::leftJoin('plans', 'plans.id', 'photos.plan_id')->whereIn('photos.user_id', $user_Ids)->where('isFree', 0)->where('photos.status', 5)->sum('plans.price') * 0.2;
        Photos::whereIn('user_id', $user_Ids)->where('status', 2)->update(['status' => 3]);
        Photos::whereIn('user_id', $user_Ids)->where('status', 6)->update(['status' => 4]);
        $user->ref_earn += $pendingPay - $refundedPay;
        $user->save();
        return redirect()->route('admin.review.request', $id);
    }

    public function analyze(Request $request)
    {
        $from = $request->from ?? Carbon::today()->subDays(7)->format("Y-m-d");
        $to = $request->to ?? Carbon::today()->format("Y-m-d");

        // $data = User::select('*', DB::raw('COUNT(*) as total_signups'), DB::raw('SUM(photos_count) as total_sale'))->withCount(['photos'])->groupBy('utm_source', 'utm_medium', 'utm_campaign', 'utm_content');
        $data = User::select('*')->withCount(['photos' => fn ($query) => $query->where('status', '!=', '4'), 'photos as plan1' => fn ($query) => $query->where('status', '!=', '4')->whereIn('plan_id', ['1', '4', '5']),'photos as plan2' => fn ($query) => $query->where('status', '!=', '4')->where('plan_id', '2'),'photos as plan3' => fn ($query) => $query->where('status', '!=', '4')->where('plan_id', '3')]);

        if(!empty($from) && !empty($from)) {
            $data = $data->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
        } else {
            $data = $data->where('created_at', '>=', Carbon::today()->subDays(7)->toDateTimeString());
        }
        $builder = DB::query();
        $builder->fromSub($data->toBoundSql(), 'u');
        $data = $builder->select('*', DB::raw('COUNT(*) as total_signups'), DB::raw('SUM(spent) AS tspent'), DB::raw('SUM(photos_count) AS tphotos'), DB::raw('SUM(plan1) AS tplan1'), DB::raw('SUM(plan2) AS tplan2'), DB::raw('SUM(plan3) AS tplan3'))->groupBy('utm_source', 'utm_medium', 'utm_campaign', 'utm_content')->orderByRaw('SUM(photos_count) DESC')->paginate(10);
        return Inertia::render('Admin/Analyze', [
            'data' => $data,
            'csrfToken' => csrf_token(),
            'search' => [
                'from' => $from,
                'to' => $to,
            ]
        ]);
    }
    
    public function dashboard(Request $request)
    {
        $from = $request->from ?? Carbon::today()->subDays(7)->format("Y-m-d");
        $to = $request->to ?? Carbon::today()->format("Y-m-d");

        // $data = User::select('*', DB::raw('COUNT(*) as total_signups'), DB::raw('SUM(photos_count) as total_sale'))->withCount(['photos'])->groupBy('utm_source', 'utm_medium', 'utm_campaign', 'utm_content');
        $data = User::select('*')->withCount(['photos' => fn ($query) => $query->where('status', '!=', '4'), 'photos as plan1' => fn ($query) => $query->where('status', '!=', '4')->whereIn('plan_id', ['1', '4', '5']),'photos as plan2' => fn ($query) => $query->where('status', '!=', '4')->where('plan_id', '2'),'photos as plan3' => fn ($query) => $query->where('status', '!=', '4')->where('plan_id', '3')]);

        if(!empty($from) && !empty($from)) {
            $data = $data->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
        } else {
            $data = $data->where('created_at', '>=', Carbon::today()->subDays(7)->toDateTimeString());
        }
        $builder = DB::query();
        $builder->fromSub($data->toBoundSql(), 'u');
        $data = $builder->select('*', DB::raw('COUNT(*) as total_signups'), DB::raw('SUM(spent) AS tspent'), DB::raw('SUM(photos_count) AS tphotos'), DB::raw('SUM(plan1) AS tplan1'), DB::raw('SUM(plan2) AS tplan2'), DB::raw('SUM(plan3) AS tplan3'))->groupByRaw('DATE(created_at)')->get();
        return Inertia::render('Admin/Dashboard', [
            'data' => $data,
            'csrfToken' => csrf_token(),
            'search' => [
                'from' => $from,
                'to' => $to,
            ]
        ]);
    }
}