<?php

namespace Adultdate\FilamentBooking\Filament\Resources\Booking\Users\Tables;

use Adultdate\FilamentBooking\Filament\Resources\Booking\Users\Pages\ManageServiceProviderSchedules;
use App\Models\User;
use App\UserRole;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('role')
                    ->formatStateUsing(fn ($state) => UserRole::tryFrom($state)?->label() ?? $state)
                    ->badge()
                    ->color(function ($state): ?string {
                        $role = UserRole::tryFrom($state);

                        if ($role === null) {
                            return null;
                        }

                        return match ($role) {
                            UserRole::ADMIN => 'primary',
                            UserRole::USER => 'gray',
                            UserRole::SERVICE => 'success',
                            UserRole::BOOKING => 'warning',
                        };
                    })
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('schedule')
                    ->label('Manage Schedule')
                    ->url(fn (User $record) => ManageServiceProviderSchedules::getUrl(['record' => $record->id]))
                    ->visible(fn (User $record) => UserRole::tryFrom($record->role) === UserRole::SERVICE),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
