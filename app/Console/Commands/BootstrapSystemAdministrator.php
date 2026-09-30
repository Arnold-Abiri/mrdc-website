<?php

namespace App\Console\Commands;

use App\Domain\Identity\AuditWriter;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class BootstrapSystemAdministrator extends Command
{
    protected $signature = 'security:bootstrap-admin {--email=} {--name=}';

    protected $description = 'Create the first System Administrator interactively';

    public function handle(): int
    {
        if (User::where('status', 'active')->whereHas('roles', fn ($q) => $q->where('name', 'System Administrator'))->exists()) {
            $this->error('An active System Administrator already exists.');

            return self::FAILURE;
        }
        if (! $this->input->isInteractive()) {
            $this->error('Interactive terminal required for the password.');

            return self::FAILURE;
        }
        $name = $this->option('name') ?: $this->ask('Name');
        $email = $this->option('email') ?: $this->ask('Email');
        $password = $this->secret('Password');
        $values = Validator::make(compact('name', 'email', 'password'), ['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255', 'unique:users,email'], 'password' => ['required', Password::min(16)->mixedCase()->numbers()->symbols()]])->validate();
        DB::transaction(function () use ($values): void {
            $role = Role::findByName('System Administrator', 'web');
            $user = User::create(['name' => $values['name'], 'email' => strtolower(trim($values['email'])), 'password' => Hash::make($values['password'])]);
            $user->forceFill(['status' => 'active', 'email_verified_at' => now()])->save();
            $user->assignRole($role);
            DB::table('user_role_scopes')->insert(['user_id' => $user->id, 'role_id' => $role->id, 'scope_type' => 'global', 'created_at' => now(), 'updated_at' => now()]);
            app(AuditWriter::class)->record(null, 'security.bootstrap_admin', $user);
        });
        $this->info('System Administrator created.');

        return self::SUCCESS;
    }
}
