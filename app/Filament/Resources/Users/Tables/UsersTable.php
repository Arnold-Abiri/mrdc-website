<?php

namespace App\Filament\Resources\Users\Tables;

use App\Domain\Identity\UserManager;
use App\Models\Department;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([TextColumn::make('name')->searchable(), TextColumn::make('email')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('roles.name')->badge(), TextColumn::make('department.name'), TextColumn::make('last_login_at')->dateTime()])
            ->filters([SelectFilter::make('status')->options(['active' => 'Active', 'disabled' => 'Disabled']), SelectFilter::make('department_id')->relationship('department', 'name')])
            ->recordActions([
                ViewAction::make(), EditAction::make(),
                Action::make('disable')->visible(fn (User $record) => $record->status === 'active')->authorize(fn (User $record) => Gate::allows('disable', $record))->requiresConfirmation()->action(fn (User $record) => app(UserManager::class)->setActive(auth()->user(), $record, false)),
                Action::make('reactivate')->visible(fn (User $record) => $record->status === 'disabled')->authorize(fn (User $record) => Gate::allows('disable', $record))->requiresConfirmation()->action(fn (User $record) => app(UserManager::class)->setActive(auth()->user(), $record, true)),
                Action::make('removeRole')->authorize(fn (User $record) => Gate::allows('assignRoles', $record))->form([Select::make('role_id')->options(fn (User $record) => $record->roles()->pluck('name', 'id'))->required()])->requiresConfirmation()->action(fn (User $record, array $data) => app(UserManager::class)->removeRole(auth()->user(), $record, Role::findOrFail($data['role_id']))),
                Action::make('assignRole')->authorize(fn (User $record) => Gate::allows('assignRoles', $record))->form([
                    Select::make('role_id')->options(Role::query()->pluck('name', 'id'))->required(),
                    Select::make('scope_type')->options(['global' => 'Global', 'department' => 'Department', 'own' => 'Own'])->required(),
                    Select::make('department_id')->options(Department::query()->where('status', 'active')->pluck('name', 'id')),
                ])->action(fn (User $record, array $data) => app(UserManager::class)->assignRole(auth()->user(), $record, Role::findOrFail($data['role_id']), $data['scope_type'], $data['department_id'] ?? null)),
            ]);
    }
}
