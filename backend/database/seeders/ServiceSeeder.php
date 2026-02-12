<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\User; // Assuming you have users
use App\Models\Category; // Assuming categories exist

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get freelancer and categories
        $freelancer = User::where('role', 'freelancer')->first();
        $categories = Category::all();

        if (!$freelancer || $categories->isEmpty()) {
            $this->command->info('Please seed users and categories first!');
            return;
        }

        // Sample services data
        $services = [
            ['Web Development Service', 'Build responsive websites with modern frameworks', 500.00, 1],
            ['Mobile App Development', 'iOS and Android app development', 800.00, 2],
            ['Logo Design', 'Professional logo and branding design', 150.00, 3],
            ['Content Writing', 'SEO optimized articles and blogs', 75.00, 4],
            ['Digital Marketing', 'Social media and ad campaigns', 300.00, 5],
            ['Video Editing', 'Professional video editing and animation', 200.00, 6],
            ['E-commerce Setup', 'Shopify/WooCommerce store development', 400.00, 1],
            ['UI/UX Design', 'User interface and experience design', 250.00, 3],
            ['SEO Optimization', 'Search engine optimization services', 180.00, 5],
            ['App Prototyping', 'Mobile app wireframes and prototypes', 320.00, 2],
        ];

        foreach ($services as $service) {
            Service::create([
                'title' => $service[0],
                'description' => $service[1],
                'price' => $service[2],
                'category_id' => $categories->find($service[3]) ? $service[3] : $categories->first()->id,
                'user_id' => $freelancer->id,
                'is_active' => true
            ]);
        }
    }
}
