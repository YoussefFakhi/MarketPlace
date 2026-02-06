<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample categories - slug will be auto-generated
        Category::create(['name' => 'Web Development', 'slug' => 'web-development']);
        Category::create(['name' => 'Mobile Apps', 'slug' => 'mobile-apps']);
        Category::create(['name' => 'Design', 'slug' => 'design']);
        Category::create(['name' => 'Writing', 'slug' => 'writing']);
        Category::create(['name' => 'Marketing', 'slug' => 'marketing']);
        Category::create(['name' => 'Video & Animation', 'slug' => 'video-animation']);
    }
}
