<?php

namespace App\Filament\Resources\Faqs;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Clusters\Content\ContentCluster;
use App\Filament\Resources\Faqs\Pages\CreateFaq;
use App\Filament\Resources\Faqs\Pages\EditFaq;
use App\Filament\Resources\Faqs\Pages\ListFaqs;
use App\Filament\Resources\Faqs\Schemas\FaqForm;
use App\Filament\Resources\Faqs\Tables\FaqsTable;
use App\Models\Faq;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery(), auth()->user(), 'faqs.view', 'department_id', null);
    }

    protected static ?string $cluster = ContentCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static \UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'FAQs';

    protected static ?string $modelLabel = 'FAQ';

    protected static ?string $pluralModelLabel = 'FAQs';

    public static function form(Schema $schema): Schema
    {
        return FaqForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FaqsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFaqs::route('/'),
            'create' => CreateFaq::route('/create'),
            'edit' => EditFaq::route('/{record}/edit'),
        ];
    }
}
