<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $posts = [];

        for ($i = 0; $i < 100; $i++) {
            $createdAt = $faker->dateTimeBetween('-2 years', 'now');

            $posts[] = [
                'title' => $faker->sentence(6),
                'body' => $faker->paragraphs(3, true),
                'author' => $faker->name(),
                'published_at' => $faker->optional(0.8)->dateTimeBetween('-1 year', 'now'),
                'created_at' => $createdAt,
                'updated_at' => $faker->dateTimeBetween($createdAt, 'now')
            ];
        }

        collect($posts)->chunk(25)->each(function ($chunk) {
            DB::table('posts')->insert($chunk->toArray());
        });
    }
}
