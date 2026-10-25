<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('نام محصول')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category')
                    ->label('دسته‌بندی')
                    ->searchable(),

                TextColumn::make('brand')
                    ->label('برند')
                    ->searchable(),

                TextColumn::make('price')
                    ->label('قیمت')
                    ->money('IRR', divideBy: 1)
                    ->sortable(),

                TextColumn::make('stock')
                    ->label('موجودی')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state < 5 ? 'danger' : 'success'),

                IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}