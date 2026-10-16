<?php

namespace App\Filament\Resources\Tenders;

use App\Domain\Cms\TenderManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Clusters\Services\ServicesCluster;
use App\Filament\Concerns\HandlesFeaturedImageUpload;
use App\Filament\Concerns\TranslationFields;
use App\Filament\Resources\Tenders\Pages\CreateTender;
use App\Filament\Resources\Tenders\Pages\EditTender;
use App\Filament\Resources\Tenders\Pages\ListTenders;
use App\Models\Tender;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
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

class TenderResource extends Resource
{
    protected static ?string $model = Tender::class;

    protected static ?string $cluster = ServicesCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static \UnitEnum|string|null $navigationGroup = 'Services & Development';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Tenders';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('reference')->required()->maxLength(60)->helperText('Official tender reference, e.g. MRDC/2026/014.'),
            TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, callable $set): mixed => $set('slug', HandlesFeaturedImageUpload::slugFromTitle($state))),
            TextInput::make('slug')
                ->required()
                ->maxLength(160)
                ->unique(ignoreRecord: true)
                ->helperText('Suggested from title; you can edit it.'),
            TextInput::make('category')->maxLength(60),
            MarkdownEditor::make('description')->required()->maxLength(50000)->columnSpanFull()->helperText('Formatting toolbar with live preview. Plain paragraphs, lists and links only.'),
            Select::make('lifecycle_status')->options(['upcoming' => 'Upcoming', 'open' => 'Open', 'closed' => 'Closed', 'awarded' => 'Awarded', 'cancelled' => 'Cancelled'])->required(),
            DatePicker::make('opens_at'), DateTimePicker::make('closes_at')->helperText('Public status derives from these dates: past closing means closed.'),
            Textarea::make('contact_instructions')->maxLength(5000)->helperText('How and where to submit bids. Never enter bank or payment details.'),
            Select::make('document_id')->relationship('document', 'title')->searchable()->helperText('Published tender document.'),
            Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
            TextInput::make('display_order')->numeric()->default(0)->required(),
            TranslationFields::make(Tender::translatableFields()), ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('No tenders yet')
            ->emptyStateDescription('Create your first tender — drafts stay hidden until published.')
            ->emptyStateIcon(Heroicon::OutlinedClipboardDocumentList)
            ->columns([TextColumn::make('reference')->searchable(), TextColumn::make('title')->searchable(), TextColumn::make('lifecycle_status')->badge(), TextColumn::make('award_status')->badge()->toggleable(), TextColumn::make('status')->badge(), TextColumn::make('verification_status')->badge()->toggleable(), TextColumn::make('closes_at')->dateTime()->sortable()->toggleable()])->filters([SelectFilter::make('lifecycle_status')->options(['upcoming' => 'Upcoming', 'open' => 'Open', 'closed' => 'Closed', 'awarded' => 'Awarded', 'cancelled' => 'Cancelled']), SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'unpublished' => 'Unpublished', 'archived' => 'Archived'])])->recordActions([DeleteAction::make(),
                EditAction::make(),
                Action::make('verify')->authorize(fn (Tender $record): bool => Gate::allows('verify', $record))->visible(fn (Tender $record): bool => $record->verification_status !== 'publishable')->requiresConfirmation()->action(fn (Tender $record) => app(TenderManager::class)->setVerification(auth()->user(), $record, 'publishable')),
                Action::make('publish')->authorize(fn (Tender $record): bool => Gate::allows('publish', $record))->visible(fn (Tender $record): bool => $record->status !== 'published')->requiresConfirmation()->action(fn (Tender $record) => app(TenderManager::class)->setStatus(auth()->user(), $record, 'published')),
                Action::make('unpublish')->authorize(fn (Tender $record): bool => Gate::allows('publish', $record))->visible(fn (Tender $record): bool => $record->status === 'published')->requiresConfirmation()->action(fn (Tender $record) => app(TenderManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
                Action::make('archive')->authorize(fn (Tender $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Tender $record) => app(TenderManager::class)->setStatus(auth()->user(), $record, 'archived')),
                Action::make('award')->label('Record award')->authorize(fn (Tender $record): bool => Gate::allows('award', $record))->schema([Select::make('award_status')->options(['none' => 'None', 'pending' => 'Pending', 'awarded' => 'Awarded'])->required(), TextInput::make('awarded_to')->maxLength(255)->helperText('Awarded contractor/supplier. Only approved public information.'), DatePicker::make('awarded_at'), TextInput::make('award_amount')->numeric()->helperText('Approved published amount only; leave empty if not published.'), TextInput::make('award_reference')->maxLength(60), Select::make('award_document_id')->relationship('document', 'title')->searchable(), Textarea::make('award_remarks')->maxLength(5000)])->action(fn (Tender $record, array $data) => app(TenderManager::class)->recordAward(auth()->user(), $record, $data)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery(), auth()->user(), 'tenders.view', 'department_id', 'created_by');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->where('lifecycle_status', 'open')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function getPages(): array
    {
        return ['index' => ListTenders::route('/'), 'create' => CreateTender::route('/create'), 'edit' => EditTender::route('/{record}/edit')];
    }
}
