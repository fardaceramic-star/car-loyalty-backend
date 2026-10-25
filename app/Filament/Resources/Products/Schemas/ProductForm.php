<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('نام محصول')
                    ->required()
                    ->maxLength(200),

                TextInput::make('category')
                    ->label('دسته‌بندی')
                    ->maxLength(100),

                TextInput::make('brand')
                    ->label('برند')
                    ->maxLength(100),

                TextInput::make('price')
                    ->label('قیمت (تومان)')
                    ->numeric()
                    ->required()
                    ->minValue(0),

                TextInput::make('stock')
                    ->label('موجودی')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->default(0),

                Textarea::make('description')
                    ->label('توضیحات')
                    ->columnSpanFull(),

                FileUpload::make('image_url')
                    ->label('تصویر محصول')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
            ]);
    }
}