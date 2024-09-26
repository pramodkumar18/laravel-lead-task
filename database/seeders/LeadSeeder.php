<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lead;
use App\Models\User;
use Faker\Factory as Faker;

class LeadSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 1000; $i++) {
            Lead::create([
                'title' => $faker->sentence,
                'contact' => $faker->phoneNumber,
                'email' => $faker->safeEmail,
                'name' => $faker->name,
                'type' => $faker->randomElement(['WEB', 'WALKIN', 'STORE']),
                'user_id' => User::inRandomOrder()->first()->id,
            ]);
        }
    }
}
