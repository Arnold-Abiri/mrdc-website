<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SecuritySeeder extends Seeder
{
    public const PERMISSIONS = ['admin.access', 'users.view', 'users.create', 'users.update', 'users.disable', 'users.assign_roles', 'roles.view', 'roles.create', 'roles.update', 'roles.assign_permissions', 'departments.view', 'departments.create', 'departments.update', 'departments.disable', 'audit.view', 'pages.view', 'pages.create', 'pages.update', 'pages.publish', 'pages.verify'];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
        $admin = Role::firstOrCreate(['name' => 'System Administrator', 'guard_name' => 'web']);
        $admin->syncPermissions(self::PERMISSIONS);
        foreach (['Website Administrator', 'Department Content Owner', 'Auditor'] as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
        Role::findByName('Website Administrator', 'web')->syncPermissions(['admin.access', 'departments.view']);
        Role::findByName('Department Content Owner', 'web')->syncPermissions(['admin.access', 'departments.view']);
        Role::findByName('Auditor', 'web')->syncPermissions(['admin.access', 'audit.view']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
