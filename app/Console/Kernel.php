<?php

namespace App\Console;

use App\Mail\SendPromoMail;
use App\Models\Photos;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        $schedule->call(function () {
            $now = Carbon::now();
            // User::whereNull('stripe_id')->whereDay('created_at', $now->day)->update(['balance' => '2']);
            $users = User::whereNull('stripe_id')->whereNull('plan_id')->where('account_type', 0)->where('spent', 0)->where('count', 0)->where('received_email', 0)->where('created_at', '<=', $now->subDays()->toDateTimeString())->get();
            foreach ($users as $user) {
                Mail::to($user)->send(new SendPromoMail($user));
                $user->received_email = 1;
                Log::info($user->email);
                Log::info('sent!');
                $user->save();
            }
        })->hourly();

        $schedule->call(function () {
            $photos = Photos::get();
            $imgs = [];
            $images = $photos->pluck('image_urls')->toArray();
            $rejectImages = $photos->whereNotNull('rejected_urls')->where('rejected_urls', '!=', '[]')->pluck('rejected_urls')->toArray();
            $images = array_merge($images, $rejectImages);
            foreach ($images as $image) {
                $imgs[] = json_decode($image);
            }
            $imgs = Arr::flatten($imgs);
            $imgFiles = [];
            foreach ($imgs as $img) {
                $imgFiles[] = 'public/uploads/inputs/' . $img;
            }
            $allFiles = Storage::allFiles('public/uploads/inputs');

            $diff1 = array_diff($allFiles, $imgFiles);
            // Storage::delete($diff1);
            $count = 0;
            foreach ($diff1 as $diff) {
                if(Storage::lastModified($diff) < Carbon::now()->subDays()->timestamp) {
                    $count++;
                    Storage::delete($diff);
                }
            }
            
            Log::info('[Cleaning : ] ' .  $count . ' Files Cleaned');
        })->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}