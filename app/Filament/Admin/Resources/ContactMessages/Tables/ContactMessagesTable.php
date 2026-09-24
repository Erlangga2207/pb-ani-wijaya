<?php

namespace App\Filament\Admin\Resources\ContactMessages\Tables;

use App\Enums\LeadStatus;
use App\Filament\Admin\Resources\ContactMessages\ContactMessageResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Tanggal')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->weight(fn ($record) => $record->is_read ? null : 'bold'),
                TextColumn::make('email')->label('Email')->searchable()->copyable()->toggleable(),
                TextColumn::make('phone')->label('HP')->placeholder('-')->toggleable(),
                TextColumn::make('subject')->label('Subjek')->searchable()->placeholder('(Tanpa subjek)')->limit(40),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status')->options(LeadStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()->label('Tindak lanjut'),
                ActionGroup::make(ContactMessageResource::leadActions()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
