<?php

namespace App\Filament\Resources\Shop;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Squire\Models\Country;
use App\Models\Shop\Customer;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Shop\CustomerResource\Pages;
use App\Filament\Resources\Shop\CustomerResource\RelationManagers;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static?string $recordTitleAttribute = 'name';

    protected static?string $navigationGroup = 'Shop';

    public static ?string $slug = 'shop/customers';

    public static ?int $navigationSort = 1;

    public static function form(Form $form): Form

    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                        ->label('Email Address')
                        ->required()
                        ->maxLength(255)
                        ->email()
                        ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('phone')
                        ->maxLength(255),

                        Forms\Components\DatePicker::make('birthday')
                        ->maxDate('today')
                    ])
                    ->columns(2)
                    ->columnSpan(['lg', fn (?Customer $record) => $record === null ? 3 : 2]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),

                Tables\Columns\TextColumn::make('email'),

                Tables\Columns\TextColumn::make('phone'),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
