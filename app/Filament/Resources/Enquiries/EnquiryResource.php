<?php

namespace App\Filament\Resources\Enquiries;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Resources\Enquiries\Pages\ListEnquiries;
use App\Filament\Resources\Enquiries\Pages\ViewEnquiry;
use App\Models\Department;
use App\Models\Enquiry;
use App\Models\User;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EnquiryResource extends Resource
{
    protected static ?string $model = Enquiry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('public_id')->label('Reference'),
            TextEntry::make('status')->badge(),
            TextEntry::make('submitted_at')->dateTime(),
            TextEntry::make('name'),
            TextEntry::make('email'),
            TextEntry::make('phone'),
            TextEntry::make('category'),
            TextEntry::make('department_name')->label('Department')->state(fn (Enquiry $record): ?string => $record->department instanceof Department ? $record->department->name : null),
            TextEntry::make('assignee_name')->label('Assigned to')->state(fn (Enquiry $record): ?string => $record->assignee instanceof User ? $record->assignee->name : null),
            TextEntry::make('subject'),
            TextEntry::make('message')->columnSpanFull(),
            TextEntry::make('notes')->label('Internal notes')->state(fn (Enquiry $record): string => $record->notes()->with('author')->oldest()->get()->map(fn ($note): string => $note->created_at->format('Y-m-d H:i').' · '.$note->author?->name.': '.$note->body)->implode("\n\n"))->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('public_id')->label('Reference')->searchable(),
            TextColumn::make('subject')->searchable(),
            TextColumn::make('department.name')->label('Department'),
            TextColumn::make('category'),
            TextColumn::make('status')->badge(),
            TextColumn::make('submitted_at')->dateTime()->sortable(),
        ])->defaultSort('submitted_at', 'desc')->recordUrl(fn (Enquiry $record): string => static::getUrl('view', ['record' => $record]));
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery(), auth()->user(), 'enquiries.view', 'department_id', null);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEnquiries::route('/'),
            'view' => ViewEnquiry::route('/{record}'),
        ];
    }
}
