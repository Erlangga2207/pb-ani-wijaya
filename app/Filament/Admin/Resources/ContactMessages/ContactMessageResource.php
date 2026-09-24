<?php

namespace App\Filament\Admin\Resources\ContactMessages;

use App\Enums\LeadStatus;
use App\Filament\Admin\Resources\Buyers\BuyerResource;
use App\Filament\Admin\Resources\ContactMessages\Pages\EditContactMessage;
use App\Filament\Admin\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Admin\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Filament\Admin\Resources\ContactMessages\Schemas\ContactMessageForm;
use App\Filament\Admin\Resources\ContactMessages\Tables\ContactMessagesTable;
use App\Models\ContactMessage;
use App\Services\LeadService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Pesan & Calon Pembeli';

    protected static ?string $modelLabel = 'Pesan';

    protected static ?string $pluralModelLabel = 'Pesan & Calon Pembeli';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'subject';

    public static function getNavigationBadge(): ?string
    {
        $count = ContactMessage::where('status', LeadStatus::New)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return ContactMessageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactMessagesTable::configure($table);
    }

    // Pesan hanya masuk dari form kontak website.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
            'view' => ViewContactMessage::route('/{record}'),
            'edit' => EditContactMessage::route('/{record}/edit'),
        ];
    }

    /** Aksi tindak lanjut lead, dipakai di tabel & halaman view. */
    public static function leadActions(): array
    {
        return [
            Action::make('whatsapp')
                ->label('Balas via WhatsApp')
                ->icon(Heroicon::OutlinedChatBubbleLeftEllipsis)
                ->color('success')
                ->visible(fn (ContactMessage $record) => filled($record->phone))
                ->url(fn (ContactMessage $record) => $record->whatsappUrl())
                ->openUrlInNewTab(),
            Action::make('email')
                ->label('Balas via Email')
                ->icon(Heroicon::OutlinedArrowUturnLeft)
                ->color('info')
                ->url(fn (ContactMessage $record) => 'mailto:'.$record->email.'?subject='.rawurlencode('Re: '.($record->subject ?? 'Pesan Anda')))
                ->openUrlInNewTab(),
            Action::make('convertToBuyer')
                ->label('Jadikan Pembeli')
                ->icon(Heroicon::OutlinedUserPlus)
                ->color('primary')
                ->visible(fn (ContactMessage $record) => $record->buyer_id === null && $record->status !== LeadStatus::Spam)
                ->requiresConfirmation()
                ->modalDescription('Data pembeli baru dibuat dari nama, HP, email, dan negara pada pesan ini.')
                ->action(function (ContactMessage $record) {
                    $buyer = app(LeadService::class)->convertToBuyer($record, auth()->user());
                    Notification::make()->success()->title("Pembeli \"{$buyer->name}\" dibuat")
                        ->actions([Action::make('open')->label('Buka data pembeli')->url(BuyerResource::getUrl('edit', ['record' => $buyer]))])
                        ->send();
                }),
            Action::make('spam')
                ->label('Tandai Spam')
                ->icon(Heroicon::OutlinedNoSymbol)
                ->color('gray')
                ->visible(fn (ContactMessage $record) => $record->status !== LeadStatus::Spam)
                ->requiresConfirmation()
                ->action(fn (ContactMessage $record) => app(LeadService::class)->markAsSpam($record, auth()->user())),
        ];
    }
}
