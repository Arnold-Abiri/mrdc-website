<?php

namespace App\Filament\Resources\SiteSettings;

use App\Filament\Clusters\Administration\AdministrationCluster;
use App\Filament\Resources\SiteSettings\Pages\EditSiteSetting;
use App\Filament\Resources\SiteSettings\Pages\ListSiteSettings;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static ?string $cluster = AdministrationCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static \UnitEnum|string|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Site settings';

    protected static ?string $modelLabel = 'site setting';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Setting')
                ->description('Changes are shown on the public website after you save.')
                ->schema([
                    Placeholder::make('setting_label')
                        ->label('What you are editing')
                        ->content(fn (?SiteSetting $record): string => $record === null ? 'Site setting' : $record->label),
                    TextInput::make('value')
                        ->label(fn (?SiteSetting $record): string => $record === null ? 'Value' : $record->label)
                        ->placeholder(fn (?SiteSetting $record): ?string => self::getValuePlaceholder($record))
                        ->maxLength(2000)
                        ->email(fn (?SiteSetting $record): bool => $record?->type === 'email')
                        ->url(fn (?SiteSetting $record): bool => $record?->type === 'url')
                        ->tel(fn (?SiteSetting $record): bool => $record?->type === 'phone')
                        ->visible(fn (?SiteSetting $record): bool => $record === null || ! in_array($record->type, ['textarea', 'image'], true))
                        ->helperText(fn (?SiteSetting $record): ?string => self::getValueHelpText($record)),
                    Textarea::make('value')
                        ->label(fn (?SiteSetting $record): string => $record === null ? 'Value' : $record->label)
                        ->rows(4)
                        ->maxLength(2000)
                        ->columnSpanFull()
                        ->visible(fn (?SiteSetting $record): bool => $record?->type === 'textarea')
                        ->helperText(fn (?SiteSetting $record): ?string => self::getValueHelpText($record)),
                    Placeholder::make('logo_preview')
                        ->label('Current logo')
                        ->content(fn (?SiteSetting $record): HtmlString|string => $record?->key === 'logo_media_id' && ctype_digit((string) $record->value)
                            ? new HtmlString('<img src="'.e(public_route('managed-media.show', (int) $record->value)).'" alt="Current council logo" style="max-height: 100px; max-width: 280px; object-fit: contain;">')
                            : 'No logo uploaded; the default crest is in use.')
                        ->html()
                        ->visible(fn (?SiteSetting $record): bool => $record?->key === 'logo_media_id'),
                    FileUpload::make('logo_file')
                        ->label('Upload a replacement logo')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->storeFiles(false)
                        ->maxSize(config('cms.max_upload_kb'))
                        ->helperText('Choose a JPEG, PNG, or WebP image. The current logo stays in place until you save.')
                        ->visible(fn (?SiteSetting $record): bool => $record?->key === 'logo_media_id'),
                ])
                ->columns(2),
        ]);
    }

    private static function getValueHelpText(?SiteSetting $record): ?string
    {
        return match ($record?->key) {
            'whatsapp_number' => 'Include the country code, e.g. 263771234567. Used by the WhatsApp Council Desk button.',
            'phone' => 'Include the country code so visitors can call from outside Zimbabwe.',
            'email', 'recruitment_email' => 'Enter a complete email address, e.g. name@example.com.',
            'physical_address' => 'Enter the public office address. Line breaks are preserved.',
            'office_hours' => 'Enter the hours visitors can contact or visit the council.',
            'facebook_url' => 'Paste the full Facebook page address, starting with https://.',
            'x_url' => 'Paste the full X profile address, starting with https://.',
            'youtube_url' => 'Paste the full YouTube channel address, starting with https://.',
            default => null,
        };
    }

    private static function getValuePlaceholder(?SiteSetting $record): ?string
    {
        return match ($record?->key) {
            'whatsapp_number' => '263771234567',
            'phone' => '+263 6621 1234',
            'email', 'recruitment_email' => 'name@example.com',
            'office_hours' => 'Monday to Friday, 8:00 am to 4:30 pm',
            'facebook_url' => 'https://www.facebook.com/your-page',
            'x_url' => 'https://x.com/your-account',
            'youtube_url' => 'https://www.youtube.com/@your-channel',
            default => null,
        };
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('group')->label('Section')->badge()->sortable(),
                TextColumn::make('label')->label('Setting')->searchable()->sortable(),
                TextColumn::make('value')->label('Current value')->limit(60)->searchable()->state(
                    fn (SiteSetting $record): string => $record->key === 'logo_media_id'
                        ? ($record->value ? 'Logo configured' : 'Using default crest')
                        : (filled($record->value) ? $record->value : 'Not set')
                ),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label('Section')
                    ->options(fn (): array => SiteSetting::query()
                        ->distinct()
                        ->orderBy('group')
                        ->pluck('group', 'group')
                        ->all()),
            ])
            ->recordActions([EditAction::make()->label('Edit setting')])
            ->defaultSort('group')
            ->paginated(false);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteSettings::route('/'),
            'edit' => EditSiteSetting::route('/{record}/edit'),
        ];
    }
}
