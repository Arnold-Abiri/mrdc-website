<?php

namespace App\Filament\Resources\Investment;

use App\Domain\Cms\InvestmentManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Concerns\TranslationFields;
use App\Filament\Resources\Investment\Pages\CreateInvestmentOpportunity;
use App\Filament\Resources\Investment\Pages\EditInvestmentOpportunity;
use App\Filament\Resources\Investment\Pages\ListInvestmentOpportunities;
use App\Models\InvestmentOpportunity;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class InvestmentOpportunityResource extends Resource
{
    protected static ?string $model = InvestmentOpportunity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static ?string $navigationLabel = 'Investment';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255), TextInput::make('slug')->required()->maxLength(160)->unique(ignoreRecord: true),
            TextInput::make('sector')->maxLength(60)->helperText('e.g. Solar energy, Mining, Horticulture.'),
            Textarea::make('summary')->maxLength(1000),
            Textarea::make('description')->required()->maxLength(50000)->helperText('Plain text only. Do not state financial returns.'),
            TextInput::make('location')->maxLength(255),
            Select::make('opportunity_status')->options(['open' => 'Open', 'prospecting' => 'Prospecting', 'committed' => 'Committed', 'closed' => 'Closed'])->required(),
            Select::make('document_id')->relationship('document', 'title')->searchable(),
            Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
            TextInput::make('display_order')->numeric()->default(0)->required(),
            TranslationFields::make(InvestmentOpportunity::translatableFields()), ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->searchable(), TextColumn::make('sector'), TextColumn::make('opportunity_status')->badge(), TextColumn::make('status')->badge(), TextColumn::make('verification_status')->badge()])->filters([SelectFilter::make('opportunity_status')->options(['open' => 'Open', 'prospecting' => 'Prospecting', 'committed' => 'Committed', 'closed' => 'Closed']), SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'unpublished' => 'Unpublished', 'archived' => 'Archived'])])->recordActions([
            EditAction::make(),
            Action::make('verify')->authorize(fn (InvestmentOpportunity $record): bool => Gate::allows('verify', $record))->requiresConfirmation()->action(fn (InvestmentOpportunity $record) => app(InvestmentManager::class)->setVerification(auth()->user(), $record, 'publishable')),
            Action::make('publish')->authorize(fn (InvestmentOpportunity $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (InvestmentOpportunity $record) => app(InvestmentManager::class)->setStatus(auth()->user(), $record, 'published')),
            Action::make('unpublish')->authorize(fn (InvestmentOpportunity $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (InvestmentOpportunity $record) => app(InvestmentManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
            Action::make('archive')->authorize(fn (InvestmentOpportunity $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (InvestmentOpportunity $record) => app(InvestmentManager::class)->setStatus(auth()->user(), $record, 'archived')),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery(), auth()->user(), 'investment.view', 'department_id', 'created_by');
    }

    public static function getPages(): array
    {
        return ['index' => ListInvestmentOpportunities::route('/'), 'create' => CreateInvestmentOpportunity::route('/create'), 'edit' => EditInvestmentOpportunity::route('/{record}/edit')];
    }
}
