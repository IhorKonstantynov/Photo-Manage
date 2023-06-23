<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic', 
                'slug' => 'Basic', 
                // 'stripe_plan' => 'price_1Mxu1QIhTKltFnGqCAOoqznp', 
                'stripe_plan' => 'price_1MZyWaLPXwUNv4jYyBH2Pc4X', 
                'price' => 25,
                'services' => "40 Unique Headshots_Full Color_Wearing Suit, Coat & Blazer_3 Unique Backgrounds_Ready in 90 minutes",
                'prompts' => 10,
                'description' => 'If you’re just getting started, this is the option for you.'
            ],
            [
                'name' => 'Premium', 
                'slug' => 'Premium', 
                // 'stripe_plan' => 'price_1Mxu4oIhTKltFnGqhsHQ0QNT', 
                'stripe_plan' => 'price_1MZyXsLPXwUNv4jYlhDNDVTW', 
                'price' => 55, 
                'services' => "80 Unique Headshots_Full Color_6 Clothing Styles_8 Unique Backgrounds_Ready in 45 Minutes",
                'prompts' => 10, 
                'description' => 'If you want a variety of HQ photos to choose from.'
            ],
            [
                'name' => 'Professional', 
                'slug' => 'Professional', 
                // 'stripe_plan' => 'price_1Mxu5pIhTKltFnGqwT0eilkG', 
                'stripe_plan' => 'price_1MZyYqLPXwUNv4jYOZWPv2we', 
                'price' => 155, 
                'services' => "160 Unique Headshots_12 Backgrounds Styles_8 Clothing Styles_Ready in 30 Minutes_Unlimited Customizations_24/7 Chat Support",
                'prompts' => 15,
                'description' => 'Get a full range of professional headshots with curated results.'
            ],
            [
                'name' => 'Single', 
                'slug' => 'Beginner', 
                // 'stripe_plan' => 'price_1Mxu5pIhTKltFnGqwT0eilkG', 
                'stripe_plan' => 'price_1MZyYqLPXwUNv4jYOZWPv2we', 
                'price' => 25,
                'services' => "40 Unique Headshots_Full Color_Wearing Suit, Coat & Blazer_3 Unique Backgrounds_Ready in 30 Minutes_24/7 Chat Support",
                'credit' => 0,
                'prompts' => 10,
                'account_type' => 1,
                'description' => 'If you’re just getting started, this is the option for you.'
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'Enterprise',
                // 'stripe_plan' => 'price_1Mxu5pIhTKltFnGqwT0eilkG', 
                'stripe_plan' => 'price_1MZyYqLPXwUNv4jYOZWPv2we', 
                'price' => 25,
                'services' => "80 Unique Headshots_12 Backgrounds Styles_8 Clothing Styles_Ready in 30 Minutes_Unlimited Customizations_24/7 Chat Support_1 Background Customizer credit",
                'credit' => 1,
                'prompts' => 20,
                'account_type' => 1,
                'description' => 'Get a full range of professional headshots with curated results.'
            ]
        ];
  
        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
