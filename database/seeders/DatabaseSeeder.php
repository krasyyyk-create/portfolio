<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $categories = Category::factory()->createMany([
            ['name' => 'Architecture', 'slug' => 'architecture'],
            ['name' => 'DevOps', 'slug' => 'devops'],
            ['name' => 'Laravel', 'slug' => 'laravel'],
        ]);

        Post::factory(3)->create(['user_id' => $admin->id])
            ->each(fn (Post $post) => $post->categories()->attach(
                $categories->random(rand(1, 2))->pluck('id')
            ));

        Post::factory()->draft()->create([
            'user_id' => $admin->id,
            'title' => 'Draft: Upcoming Infrastructure Deep Dive',
        ])->categories()->attach($categories->where('slug', 'devops')->pluck('id'));
    }
}
