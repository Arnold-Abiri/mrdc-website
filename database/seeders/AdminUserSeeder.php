<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD', 'ChangeMe123!');

        $admin = User::firstOrCreate(
            ['email' => 'admin@mutokordc.co.zw'],
            [
                'name' => 'Council Administrator',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );

        if ($admin->wasRecentlyCreated === false && env('ADMIN_PASSWORD')) {
            $admin->forceFill(['password' => Hash::make($password)])->save();
        }

        $admin->assignRole('System Administrator');
        $this->grantGlobalScope($admin);

        $arnold = User::firstOrCreate(
            ['email' => 'arnoldabiri01@gmail.com'],
            [
                'name' => 'Arnold Abiri',
                'password' => Hash::make('Jason#2024'),
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );

        $arnold->assignRole('System Administrator');
        $this->grantGlobalScope($arnold);
    }

    private function grantGlobalScope(User $user): void
    {
        $role = Role::findByName('System Administrator');

        DB::table('user_role_scopes')->updateOrInsert(
            ['user_id' => $user->id, 'role_id' => $role->id],
            ['scope_type' => 'global', 'department_id' => null, 'created_at' => now(), 'updated_at' => now()]
        );
    }
}
