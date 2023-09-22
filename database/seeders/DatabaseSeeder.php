<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $faker = FakerFactory::create();
            $response = (new UserController)->create(new Request([
                "user" => $faker->name(),
                "name" => $faker->name(),
                "phone" => $faker->numerify('##########'),
                "password" => Str::random(10),
                "consent_id1" => true,
                "consent_id2" => $faker->randomElement([true,false]),
                "consent_id3" => $faker->randomElement([true,false])
            ]));
        }
    }
}
