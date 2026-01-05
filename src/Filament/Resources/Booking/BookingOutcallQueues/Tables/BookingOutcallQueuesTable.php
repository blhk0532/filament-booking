<?php

namespace Adultdate\FilamentBooking\Filament\Resources\Booking\BookingOutcallQueues\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class BookingOutcallQueuesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('street')
                    ->searchable(),
                TextColumn::make('city')
                    ->searchable(),
                //        TextColumn::make('maps')
                //            ->searchable(),
                //        TextColumn::make('age')
                //            ->numeric()
                //            ->sortable(),
                //        TextColumn::make('sex')
                //            ->searchable(),
                //        TextColumn::make('dob')
                //            ->date()
                //            ->sortable(),
                //     TextColumn::make('phone')
                //    ->searchable(),
               PhoneColumn::make('phone')->displayFormat(PhoneInputNumberType::NATIONAL)->defaultCountry('SE') ->countryColumn('SE'),
                TextColumn::make('status')
                    ->searchable(),
                //        TextColumn::make('type')
                //            ->searchable(),
                //        TextColumn::make('result')
                //            ->searchable(),
                //        TextColumn::make('attempts')
                //            ->numeric()
                //            ->sortable(),
                //        TextColumn::make('user_id')
                //            ->numeric()
                //            ->sortable(),
                //        TextColumn::make('service_user_id')
                //            ->numeric()
                //            ->sortable(),
                //        TextColumn::make('booking_user_id')
                //            ->numeric()
                //            ->sortable(),
                //        TextColumn::make('start_time')
                //            ->dateTime()
                //            ->sortable(),
                //        TextColumn::make('end_time')
                //            ->dateTime()
                //            ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                //        TextColumn::make('created_at')
                //            ->dateTime()
                //            ->sortable()
                //            ->toggleable(isToggledHiddenByDefault: true),
                //        TextColumn::make('updated_at')
                //            ->dateTime()
                //            ->sortable()
                //            ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
