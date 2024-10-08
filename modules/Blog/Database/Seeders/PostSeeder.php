<?php

namespace Modules\Blog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Blog\Models\Post;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::factory(8)->create();
        Post::factory(1)->create([
            'status' => 'draft'
        ]);
        Post::factory(1)->create([
            'status' => 'under-review'
        ]);
    }
}
