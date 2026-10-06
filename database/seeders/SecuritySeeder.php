<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SecuritySeeder extends Seeder
{
    public const PERMISSIONS = ['admin.access', 'users.view', 'users.create', 'users.update', 'users.disable', 'users.assign_roles', 'roles.view', 'roles.create', 'roles.update', 'roles.assign_permissions', 'departments.view', 'departments.create', 'departments.update', 'departments.disable', 'audit.view', 'pages.view', 'pages.create', 'pages.update', 'pages.publish', 'pages.verify', 'enquiries.view', 'enquiries.update', 'enquiries.route', 'enquiries.assign', 'media.view', 'media.create', 'media.update', 'documents.view', 'documents.create', 'documents.update', 'documents.publish', 'documents.verify', 'services.view', 'services.create', 'services.update', 'services.publish', 'services.verify', 'departments.publish', 'departments.verify', 'editorial.view', 'editorial.create', 'editorial.update', 'editorial.publish', 'editorial.verify', 'wards.view', 'wards.create', 'wards.update', 'wards.publish', 'wards.verify', 'officials.view', 'officials.create', 'officials.update', 'officials.publish', 'officials.verify', 'contacts.view', 'contacts.create', 'contacts.update', 'contacts.publish', 'contacts.verify', 'tenders.view', 'tenders.create', 'tenders.update', 'tenders.publish', 'tenders.verify', 'vacancies.view', 'vacancies.create', 'vacancies.update', 'vacancies.publish', 'vacancies.verify', 'investment.view', 'investment.create', 'investment.update', 'investment.publish', 'investment.verify', 'statistics.view', 'statistics.create', 'statistics.update', 'meetings.view', 'meetings.create', 'meetings.update', 'meetings.publish', 'meetings.verify', 'slides.view', 'slides.create', 'slides.update', 'slides.publish'];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
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
