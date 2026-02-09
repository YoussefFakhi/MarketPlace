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
    public function run(): void
    {
        // Get existing users and categories
        $users = User::all();
        $categories = Category::all();

        if ($users->isEmpty() || $categories->isEmpty()) {
            $this->command->info('Please seed users and categories first!');
            return;
        }

        // Create sample services
        Service::create([
            'title' => 'Professional Website Design',
            'description' => 'I will create a responsive, modern website design tailored to your business needs with custom graphics and user-friendly interface.',
            'price' => 150.00,
            'category_id' => $categories->first()->id, // Web Development category
            'user_id' => $users->first()->id, // First user as owner
            'is_active' => true,
        ]);

        Service::create([
            'title' => 'Mobile App Development',
            'description' => 'Build a cross-platform mobile application for iOS and Android with clean UI/UX and full functionality.',
            'price' => 800.00,
            'category_id' => $categories->find(2)?->id ?? $categories->first()->id, // Mobile Apps category
            'user_id' => $users->first()->id,
            'is_active' => true,
        ]);

        Service::create([
            'title' => 'Logo Design & Branding',
            'description' => 'Create a unique, professional logo and brand identity package including color palette and fonts.',
            'price' => 75.00,
            'category_id' => $categories->find(3)?->id ?? $categories->first()->id, // Design category
            'user_id' => $users->skip(1)->first()?->id ?? $users->first()->id, // Second user if available
            'is_active' => true,
        ]);

        Service::create([
            'title' => 'Content Writing',
            'description' => 'Professional blog posts, articles, and copywriting services with SEO optimization and engaging content.',
            'price' => 50.00,
            'category_id' => $categories->find(4)?->id ?? $categories->first()->id, // Writing category
            'user_id' => $users->skip(1)->first()?->id ?? $users->first()->id,
            'is_active' => true,
        ]);

        Service::create([
            'title' => 'Social Media Marketing',
            'description' => 'Complete social media marketing strategy including content creation, scheduling, and engagement analysis.',
            'price' => 200.00,
            'category_id' => $categories->find(5)?->id ?? $categories->first()->id, // Marketing category
            'user_id' => $users->last()->id, // Last user
            'is_active' => true,
        ]);
    }
}
