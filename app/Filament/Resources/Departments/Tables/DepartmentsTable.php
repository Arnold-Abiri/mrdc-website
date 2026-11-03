<?php

namespace App\Filament\Resources\Departments\Tables;

use App\Domain\Identity\DepartmentManager;
use App\Models\Department;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class DepartmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('code')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('public_status')->badge(), TextColumn::make('public_verification_status')->badge()])->recordActions([
            ViewAction::make(),
            EditAction::make(),
            Action::make('verifyPublic')
                ->authorize(fn (Department $record): bool => Gate::allows('verify', $record))
                ->visible(fn (Department $record): bool => $record->public_verification_status !== 'publishable')
                ->requiresConfirmation()
                ->action(fn (Department $record) => app(DepartmentManager::class)->setPublicVerification(auth()->user(), $record, 'publishable')),
            Action::make('publishPublic')
                ->authorize(fn (Department $record): bool => Gate::allows('publish', $record))
                ->visible(fn (Department $record): bool => $record->public_status !== 'published')
                ->requiresConfirmation()
                ->action(fn (Department $record) => app(DepartmentManager::class)->setPublicStatus(auth()->user(), $record, 'published')),
            Action::make('unpublishPublic')
                ->authorize(fn (Department $record): bool => Gate::allows('publish', $record))
                ->visible(fn (Department $record): bool => $record->public_status === 'published')
                ->requiresConfirmation()
                ->action(fn (Department $record) => app(DepartmentManager::class)->setPublicStatus(auth()->user(), $record, 'unpublished')),
            Action::make('deactivate')
                ->label('Deactivate')
                ->authorize(fn (Department $record): bool => Gate::allows('disable', $record))
                ->visible(fn (Department $record): bool => $record->status === 'active')
                ->requiresConfirmation()
                ->action(fn (Department $record) => app(DepartmentManager::class)->setActive(auth()->user(), $record, false)),
            Action::make('activate')
                ->label('Activate')
                ->authorize(fn (Department $record): bool => Gate::allows('update', $record))
                ->visible(fn (Department $record): bool => $record->status !== 'active')
                ->requiresConfirmation()
                ->action(fn (Department $record) => app(DepartmentManager::class)->setActive(auth()->user(), $record, true)),
        ]);
    }
}
