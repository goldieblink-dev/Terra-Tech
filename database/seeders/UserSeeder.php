<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@terratech.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Password123!'),
            ]
        );

        $superAdmin->syncRoles(['super_admin']);
    }
}
