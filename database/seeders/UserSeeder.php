<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\UserRole;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ownerRoleId = Role::where('name', 'Owner')->first()->id;

        $user = User::create([
            'name' => 'Achmad Rifqi',
            'email' => 'achmadrifqi09@gmail.com',
            'phone' => '081231838322',
            'password' => Hash::make('password')
        ]);

        UserRole::create([
            'user_id' => $user['id'],
            'role_id' => $ownerRoleId
        ]);
    }
}
