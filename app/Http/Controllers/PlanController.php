<?php

namespace App\Http\Controllers;

use App\Mail\RefundReqMail;
use App\Meta;
use App\Mail\RefundMail;
use App\Models\Photos;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\Plan;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Http;

class PlanController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index(): Response
    {
        $user = auth()->user();
        $plans = Plan::where('account_type', $user->account_type)->get();

        $defaultPayment = $user->defaultPaymentMethod();

        Meta::addMeta('title', env('APP_NAME') . ' | Plans');

        return Inertia::render('Plans/Lists', [
            'plans' => $plans,
            'mustVerifyEmail' => auth()->user() instanceof MustVerifyEmail,
            'is_beginner' => !isset($defaultPayment)
        ]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function show(Plan $plan, Request $request)
    {
        $intent = auth()->user()->createSetupIntent();

        Meta::addMeta('title', env('APP_NAME') . ' | Plans - ' . $plan->name);

        return Inertia::render('Plans/Subscription', [
            'plan' => $plan,
            'count' => $request->count,
            'intent' => $intent,
            'stripe_pk' => env('STRIPE_KEY'),
            'message' => session('message'),
        ]);
    }
    /**
     * Write code on Method
     * 
     */
    public function charge(Request $request)
    {
        $user = $request->user();
        $plan = Plan::find($request->plan);
        $message = "Successfully charged.";
        $defaultPayment = $user->defaultPaymentMethod();
        $defaultPayment = $defaultPayment ?? [];
        try {
            $user->createOrGetStripeCustomer();
            $user->updateDefaultPaymentMethod($request->token ?? $defaultPayment->id);
            // $user->newSubscription(env('STRIPE_NAME'), $plan->stripe_plan)->create($request->token ?? $defaultPayment->id);
            $price = $plan->price * 100;
            if ($user->isDiscount()) {
                $price = $price * 0.8;
            }
            if ($user->isEnterprise() && $plan->slug != 'Beginner') {
                $count = 1;
                if ($request->count > 0) {
                    $count = $request->count;
                }
                $price *= $count;
            }
            $payment = $user->charge($price, $request->token ?? $defaultPayment->id);
            $user->last_payment = $payment->id;
            if ($user->isEnterprise() && $plan->slug != 'Beginner') {
                $count = 1;
                if ($request->count > 0) {
                    $count = $request->count;
                }
                $user->available_employee += $count;
                $user->credit += $count;
            } else {
                $user->plan_id = $plan->id;
            }
            $user->save();
            // usleep(500000);
            if ($user->isEnterprise() && $plan->slug != 'Beginner') {
                return redirect()->route('enterprise.employees');
            }
            return redirect()->route('home');
        } catch (\Exception $exception) {
            return redirect()->route('plans.show', $plan->slug)->with('message', $exception->getMessage());
        }
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function buyCredit(Request $request)
    {
        $prev = $request->to ?? url()->previous();
        session()->put('prevUrl', $prev);
        $user = auth()->user();
        $defaultPayment = $user->defaultPaymentMethod();

        $intent = auth()->user()->createSetupIntent();

        Meta::addMeta('title', env('APP_NAME') . ' | Buy Credit');

        return Inertia::render('Plans/Credit', [
            'intent' => $intent,
            'stripe_pk' => env('STRIPE_KEY'),
            'isPayment' => isset($defaultPayment),
            'message' => session('message'),
        ]);
    }

    /**
     * Write code on Method
     *
     */
    public function payCredit(Request $request)
    {
        $prices = [
            ['credit' => 1, 'price' => 2.99],
            ['credit' => 6, 'price' => 9.99],
        ];
        $count = $request->count ?? 0;
        $user = auth()->user();
        $defaultPayment = $user->defaultPaymentMethod();

        Meta::addMeta('title', env('APP_NAME') . ' | Buy Credit');
        $defaultPayment = $defaultPayment ?? [];
        try {
            $user->createOrGetStripeCustomer();
            $user->updateDefaultPaymentMethod($request->token ?? $defaultPayment->id);
            // $user->newSubscription(env('STRIPE_NAME'), $plan->stripe_plan)->create($request->token ?? $defaultPayment->id);

            $price = $prices[$count]['price'] * 100;

            $user->charge($price, $request->token ?? $defaultPayment->id);
            $user->setCredit($prices[$count]['credit']);
            $user->save();
            $prevUrl = session()->get('prevUrl');
            session()->forget('prevUrl');
            return redirect()->to($prevUrl ?? route('home'));
        } catch (\Exception $exception) {
            return redirect()->route('charge.buyCredit')->with('message', $exception->getMessage());
        }
    }

    /**
     * Write code on Method
     */
    public function buyMore(Request $request)
    {
        $id = $request->id;
        $photo = Photos::where('user_id', auth()->user()->id)->find($id);
        if (!isset($photo)) {
            return back();
        }
        $prev = $request->to ?? url()->previous();
        session()->put('prevUrl', $prev);
        $user = auth()->user();
        $defaultPayment = $user->defaultPaymentMethod();

        $intent = auth()->user()->createSetupIntent();

        Meta::addMeta('title', env('APP_NAME') . ' | Buy 40 More Headshots');

        return Inertia::render('Plans/Buy', [
            'intent' => $intent,
            'stripe_pk' => env('STRIPE_KEY'),
            'isPayment' => isset($defaultPayment),
            'message' => session('message'),
            'photoId' => $id,
        ]);
    }

    /**
     * Write code on Method
     *
     */
    public function payMore(Request $request)
    {
        $id = $request->id;
        $user = auth()->user();
        $photo = Photos::where('user_id', $user->id)->find($id);
        if (!isset($photo) || $photo->add_more == 1) {
            return back();
        }
        $defaultPayment = $user->defaultPaymentMethod();

        $defaultPayment = $defaultPayment ?? [];
        try {
            $user->createOrGetStripeCustomer();
            $user->updateDefaultPaymentMethod($request->token ?? $defaultPayment->id);
            // $user->newSubscription(env('STRIPE_NAME'), $plan->stripe_plan)->create($request->token ?? $defaultPayment->id);

            $price = 9.99 * 100;

            $user->charge($price, $request->token ?? $defaultPayment->id);
            $user->spent += 9.99;
            $user->save();
            // ADD more prompt.
            $addSetting = "";
            if ($photo->eye != 'brown') {
                $addSetting = ", with " . $photo->eye . " eyes.";
            }
            $texts = [
                'Close up portrait photo of a sks ' . $photo->type . ', wearing a suit, color, Martin Schoeller, serious eyes, perfect eyes, cinematic, 80mm portrait photography, dramatic lighting photography, national geographic, portrait, photo, photography, Stoic, cinematic, 4k, epic, detailed photograph, shot on kodak detailed, bokeh, cinematic, hbo, dark moody, volumetric fog' . $addSetting,
                'close up studio portrait of a sks ' . $photo->type . ', wearing a suit, color, Martin Schoeller, serious eyes, perfect eyes, cinematic, 80mm portrait photography, dramatic lighting photography, national geographic, portrait, photo, photography, Stoic, cinematic, 4k, epic, detailed photograph, shot on kodak detailed, bokeh, cinematic, hbo, dark moody, volumetric fog' . $addSetting,
                'close up professional shot photo of sks ' . $photo->type . ' in analog style . dramatic . ( sunlight ) lighting highly detailed, sharp focus on eyes, intact eyes, symmetrical eyes, realistic skin texture, sharp face, detailed hair, symmetrical, intricate details, elegant, 8k high definition. Strong bokeh. Wearing professional clothing' . $addSetting,
                'closeup professional portrait of sks ' . $photo->type . ' in Biome reserves, Flash-forward lighting, Vintage atmosphere, round face, wearing professional clothing' . $addSetting,
                'analog style, professional portrait of sks ' . $photo->type . ', epic (photo, studio lighting, hard light, sony a7, 50 mm, matte skin, pores, colors, hyperdetailed, hyperrealistic)' . $addSetting
            ];

            $negativePrompt = '(deformed iris, deformed pupils, semi-realistic, cgi, 3d, render, sketch, cartoon, drawing, anime:1.4), text, close up, cropped, out of frame, worst quality, low quality, jpeg artifacts, ugly, duplicate, morbid, mutilated, extra fingers, mutated hands, poorly drawn hands, poorly drawn face, mutation, deformed, blurry, dehydrated, bad anatomy, bad proportions, extra limbs, cloned face, disfigured, gross proportions, malformed limbs, missing arms, missing legs, extra arms, extra legs, fused fingers, too many fingers, long neck';

            foreach ($texts as $key => $text) {
                $formData = [
                    'prompt' => [
                        'text' => $text,
                        'negative_prompt' => $negativePrompt,
                        'num_images' => 8,
                        'face_correct' => true,
                        'super_resolution' => true,
                        'hires_fix' => true,
                        'callback' => env('ASTRIA_CALLBACK_URL') . "prompt_created/{$photo->id}/15/" . auth()->user()->id
                    ]
                ];
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('ASTRIA_KEY')
                ])->post(env('ASTRIA_DOMAIN') . '/tunes/' . $photo->request_id . '/prompts', $formData);

                // $promptResponse = json_decode($response->body(), true);
            }
            $photo->add_more = 1;
            $photo->save();
            // END
            $prevUrl = session()->get('prevUrl');
            session()->forget('prevUrl');
            return redirect()->to($prevUrl ?? route('home'))->with('message', 'Please refresh this page in 15 minutes for your 40 new headshots!');
        } catch (\Exception $exception) {
            return redirect()->route('charge.buyMore')->with('message', $exception->getMessage());
        }
    }

    public function userRefund()
    {
        $user = auth()->user();
        $photo = $user->photos()->orderBy('created_at', 'DESC')->first();
        return Inertia::render('Plans/Refund', [
            'photo' => $photo,
            'message' => session('message')
        ]);
    }

    public function processRefund()
    {
        $user = auth()->user();

        if (isset($user->plan_id) || !isset($user->last_payment) || $user->last_payment == 'refunded') {
            return back()->with('message', __('res.Something'));
        }
        $photo = $user->photos()->orderBy('created_at', 'DESC')->first();
        if (!isset($photo->downloaded)) {
            $payment = $user->refund($user->last_payment);
            $user->last_payment = 'refunded';
            if ($photo->status == 3) {
                $photo->status = 5;
            } else {
                $photo->status = 4;
            }
            $photo->save();
            $user->spent -= $payment->amount / 100;
            $user->save();
            Mail::to($user)->send(new RefundMail($user));
            return back()->with('message', __('res.RefundSuccess'));
        }
        Mail::to('hello@prophotos.ai')->send(new RefundReqMail($user));
        return back()->with('message', __('res.RefundReqSuccess'));
    }

    public function refund(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);
        if (!isset($user) || !isset($user->last_payment) || $user->last_payment == 'refunded') {
            return back()->with('message', __('res.Something'));
        }
        $payment = $user->refund($user->last_payment);
        $user->last_payment = 'refunded';
        $photo = $user->photos()->orderBy('created_at', 'DESC')->first();
        if ($photo->status == 3) {
            $photo->status = 5;
        } else {
            $photo->status = 4;
        }
        $photo->save();
        $user->spent -= $payment->amount / 100;
        $user->save();
        Mail::to($user)->send(new RefundMail($user));
        return back()->with('message', __('res.RefundProcessing'));
    }
}