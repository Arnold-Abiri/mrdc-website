<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $permissions = ['admin.access', 'users.view', 'users.create', 'users.update', 'users.disable', 'users.assign_roles', 'roles.view', 'roles.create', 'roles.update', 'roles.assign_permissions', 'departments.view', 'departments.create', 'departments.update', 'departments.disable', 'audit.view'];
            foreach ($permissions as $name) {
                Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
            }
            $role = Role::firstOrCreate(['name' => 'System Administrator', 'guard_name' => 'web']);
            $role->syncPermissions($permissions);
            User::where('is_admin', true)->orderBy('id')->chunkById(100, function ($users) use ($role): void {
                foreach ($users as $user) {
                    $user->assignRole($role);
                    DB::table('user_role_scopes')->updateOrInsert(['user_id' => $user->id, 'role_id' => $role->id], ['scope_type' => 'global', 'department_id' => null, 'created_at' => now(), 'updated_at' => now()]);
                }
            });
        });
    }

    public function down(): void
    { /* Keep migrated grants on rollback. */
    }
};
