<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin
        User::firstOrCreate(
            ['username' => 'admin'], // Cek apakah username admin sudah ada
            [
                'name'        => 'Admin',
                'email'       => 'admin@example.com',
                'password'    => Hash::make('kasir'),
                'role'        => 'admin',
                'employee_id' => 'ADM001',
                'is_active'   => true,
            ]
        );

        // Create cashier
        User::firstOrCreate(
            ['username' => 'cashier'],
            [
                'name'        => 'Cashier',
                'email'       => 'cashier@example.com',
                'password'    => Hash::make('kasir'),
                'role'        => 'cashier',
                'employee_id' => 'CSR001',
                'is_active'   => true,
            ]
        );

        // Tambahkan lebih banyak user dengan factory
        User::factory()->count(5)->create();

    }
}
