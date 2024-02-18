<?php

namespace App\Filament\Clusters\Products\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Shop\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class VariationsRelationManager extends RelationManager
{
    protected static string $relationship = 'variations';

    public function form(Form $form): Form
    {
        // First, build the dynamic schema array
        $dynamicSchema = [];

        foreach (Attribute::all() as $attribute) {
            $dynamicSchema[] = Forms\Components\Select::make($attribute->name)
                ->options(function (Builder $query) use ($attribute) {
                    return $attribute->values()->pluck('value', 'id');
                });
        }
        return $form
            ->schema([
                Forms\Components\Section::make()
                ->schema($dynamicSchema)
                ->columns(2),

                Forms\Components\TextInput::make('sku')
                ->maxLength(255)
                ->label('SKU (Stock Keeping Unit)')
                ->unique(Product::class, 'sku', ignoreRecord:true),

                Forms\Components\TextInput::make('price')
                ->required()
                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                ->numeric(),

                Forms\Components\TextInput::make('qty')
                ->label('Quantity')
                ->required()
                ->numeric()
                ->rules(['integer', 'min:0']),
                
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
