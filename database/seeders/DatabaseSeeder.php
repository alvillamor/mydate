<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        /** @var Generator $faker */
        $faker = fake();

        foreach (range(1, 25) as $number) {
            $gender = $faker->randomElement(['Male', 'Female']);
            $user = User::updateOrCreate(
                ['email' => "dating.user.{$number}@example.com"],
                ['name' => $faker->name(), 'password' => Hash::make('password'), 'email_verified_at' => now()],
            );

            Profile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'age' => $faker->numberBetween(21, 55),
                    'bio' => $faker->realText(130),
                    'gender' => $gender,
                    'looking_for' => $faker->randomElement(['Male', 'Female', 'Everyone']),
                    'photo_path' => null,
                    'photo_url' => $number % 3 === 0 ? null : "https://i.pravatar.cc/300?img={$number}",
                ],
            );
        }
    }
}
