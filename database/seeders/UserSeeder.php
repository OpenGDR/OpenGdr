<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminExist = User::where('level', '=', User::LEVEL_ADMIN)->count();

        if ($adminExist == 0) {
            $admin = User::create([
                'username' => 'admin',
                'email' => 'admin@test.it',
                'date_of_birth' => '1985-05-25',
                'password' => \Illuminate\Support\Facades\Hash::make('admin'),
                'email_verified_at' => now(),
                'level' => User::LEVEL_ADMIN,
                'remember_token' => \Illuminate\Support\Str::random(10),
            ]);
        }

        User::factory(10)->create();
    }
}
