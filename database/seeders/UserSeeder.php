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
                'is_admin'      => 1,
                'is_gatekeeper' => 0,
                'is_penyiar'    => 0,
                'is_pjprogram'  => 0,
            ],
            [
                'name'          => 'Gatekeeper User',
                'email'         => 'gatekeeper@radio.com',
                'password'      => Hash::make('password'),
                'role'          => 'gatekeeper',
                'is_admin'      => 0,
                'is_gatekeeper' => 1,
                'is_penyiar'    => 0,
                'is_pjprogram'  => 0,
            ],
            [
                'name'          => 'Penyiar User',
                'email'         => 'penyiar@radio.com',
                'password'      => Hash::make('password'),
                'role'          => 'penyiar',
                'is_admin'      => 0,
                'is_gatekeeper' => 0,
                'is_penyiar'    => 1,
                'is_pjprogram'  => 0,
            ],
            [
                'name'          => 'PJ Program User',
                'email'         => 'pjprogram@radio.com',
                'password'      => Hash::make('password'),
                'role'          => 'pjprogram',
                'is_admin'      => 0,
                'is_gatekeeper' => 0,
                'is_penyiar'    => 0,
                'is_pjprogram'  => 1,
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(['email' => $data['email']], $data);
        }
    }
}
