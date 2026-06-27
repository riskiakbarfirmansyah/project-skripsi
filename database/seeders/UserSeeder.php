<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed users for every role so each dashboard can be tested.
     */
    public function run(): void
    {
        $users = [
            [
                'name'          => 'Admin User',
                'email'         => 'admin@radio.com',
                'password'      => Hash::make('password'),
                'role'          => 'admin',
            ],
            [
                'name'          => 'Gatekeeper User',
                'email'         => 'gatekeeper@radio.com',
                'password'      => Hash::make('password'),
                'role'          => 'gatekeeper',
            ],
            [
                'name'          => 'Penyiar User',
                'email'         => 'penyiar@radio.com',
                'password'      => Hash::make('password'),
                'role'          => 'penyiar',
            ],
            [
                'name'          => 'PJ Program User',
                'email'         => 'pjprogram@radio.com',
                'password'      => Hash::make('password'),
                'role'          => 'pj_program',
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(['email' => $data['email']], $data);
        }
    }
}
