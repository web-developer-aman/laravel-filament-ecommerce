<?php

namespace App\Filament\Clusters\Products\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class VariationsRelationManager extends RelationManager
{
    protected static string $relationship = 'variations';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

                Forms\Components\TextInput::make('type')
                ->required()
                ->maxLength(255),

                Forms\Components\Select::make('parent_id')
                ->label('Parent')
                ->searchable()
                ->placeholder('Select parent')
                ->relationship('parent', 'name', fn(Builder $query) =>  $query->where('parent_id', NULL)),

                Forms\Components\TextInput::make('sku')
                ->maxLength(255)
                ->label('SKU (Stock Keeping Unit)')
                ->unique(Product::class, 'sku', ignoreRecord:true),

                Forms\Components\TextInput::make('price')
                ->required()
                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                ->numeric(),
                
                SpatieMediaLibraryFileUpload::make('image')
                ->image()
                ->multiple()
                ->maxfiles(4)
                ->imageEditor(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->reorderable('order')
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('image')
                ->label('Image'),
                Tables\Columns\TextColumn::make('name')
                ->sortable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('parent_id'),
                Tables\Columns\TextColumn::make('order')
                ->sortable(),
                Tables\Columns\TextColumn::make('price')
                ->label('Price')
                ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
