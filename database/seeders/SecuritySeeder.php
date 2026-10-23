<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Facades\Filament;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SecuritySeeder extends Seeder
{
    public const PERMISSIONS = ['admin.access', 'users.view', 'users.create', 'users.update', 'users.disable', 'users.assign_roles', 'roles.view', 'roles.create', 'roles.update', 'roles.assign_permissions', 'departments.view', 'departments.create', 'departments.update', 'departments.disable', 'audit.view', 'pages.view', 'pages.create', 'pages.update', 'pages.publish', 'pages.verify', 'pages.delete', 'enquiries.view', 'enquiries.update', 'enquiries.route', 'enquiries.assign', 'enquiries.delete', 'media.view', 'media.create', 'media.update', 'media.delete', 'documents.view', 'documents.create', 'documents.update', 'documents.publish', 'documents.verify', 'documents.delete', 'services.view', 'services.create', 'services.update', 'services.publish', 'services.verify', 'services.delete', 'departments.publish', 'departments.verify', 'editorial.view', 'editorial.create', 'editorial.update', 'editorial.publish', 'editorial.verify', 'editorial.delete', 'wards.view', 'wards.create', 'wards.update', 'wards.publish', 'wards.verify', 'wards.delete', 'officials.view', 'officials.create', 'officials.update', 'officials.publish', 'officials.verify', 'officials.delete', 'contacts.view', 'contacts.create', 'contacts.update', 'contacts.publish', 'contacts.verify', 'contacts.delete', 'tenders.view', 'tenders.create', 'tenders.update', 'tenders.publish', 'tenders.verify', 'tenders.delete', 'vacancies.view', 'vacancies.create', 'vacancies.update', 'vacancies.publish', 'vacancies.delete', 'investment.view', 'investment.create', 'investment.update', 'investment.publish', 'investment.verify', 'investment.delete', 'statistics.view', 'statistics.create', 'statistics.update', 'statistics.publish', 'statistics.verify', 'statistics.delete', 'faqs.view', 'faqs.create', 'faqs.update', 'faqs.delete', 'bank_accounts.view', 'bank_accounts.create', 'bank_accounts.update', 'bank_accounts.delete', 'site_settings.view', 'site_settings.update', 'incidents.view', 'incidents.create', 'incidents.update', 'incidents.delete', 'meetings.view', 'meetings.create', 'meetings.update', 'meetings.publish', 'meetings.verify', 'meetings.delete', 'slides.view', 'slides.create', 'slides.update', 'slides.publish', 'slides.delete', 'tenders.award', 'projects.view', 'projects.create', 'projects.update', 'projects.publish', 'projects.verify', 'projects.delete', 'galleries.view', 'galleries.create', 'galleries.update', 'galleries.publish', 'galleries.verify', 'galleries.delete', 'analytics.view', 'analytics.export', 'audit.export', 'system-health.view', 'mail_settings.view', 'mail_settings.update', 'mail_operations.view', 'mail_operations.retry'];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $currentPanel = Filament::getCurrentPanel();
        Filament::setCurrentPanel('admin');

        try {
            $permissionNames = array_unique(array_merge(self::PERMISSIONS, FilamentShield::getEntitiesPermissions() ?? []));
        } finally {
            Filament::setCurrentPanel($currentPanel);
        }

        foreach ($permissionNames as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
        $admin = Role::firstOrCreate(['name' => 'System Administrator', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::where('guard_name', 'web')->get());
        foreach (['Website Administrator', 'Department Content Owner', 'Auditor'] as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
        Role::findByName('Website Administrator', 'web')->syncPermissions(array_values(array_unique(array_merge(
            ['admin.access', 'departments.view', 'analytics.view'],
            collect(self::PERMISSIONS)->filter(fn (string $p): bool => str_ends_with($p, '.view'))->all(),
            ['editorial.create', 'editorial.update', 'documents.create', 'documents.update', 'media.create', 'media.update', 'services.create', 'services.update', 'pages.create', 'pages.update']
        ))));
        // Department Content Owner: create/update only within own department (enforced by DataScopeAuthorizer + policies).
        Role::findByName('Department Content Owner', 'web')->syncPermissions(['admin.access', 'departments.view', 'editorial.view', 'editorial.create', 'editorial.update', 'documents.view', 'documents.create', 'media.view', 'media.create', 'enquiries.view']);
        Role::findByName('Auditor', 'web')->syncPermissions(['admin.access', 'audit.view', 'audit.export', 'analytics.view']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
