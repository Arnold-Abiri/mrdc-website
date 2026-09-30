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
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('code')->searchable(), TextColumn::make('status')->badge()])->recordActions([ViewAction::make(), EditAction::make(), Action::make('toggleStatus')->authorize(fn (Department $record) => Gate::allows('disable', $record))->requiresConfirmation()->action(fn (Department $record) => app(DepartmentManager::class)->setActive(auth()->user(), $record, $record->status !== 'active'))]);
    }
}
