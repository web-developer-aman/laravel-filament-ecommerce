<?php

namespace App\Filament\Clusters\Products\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Shop\Brand;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use App\Filament\Clusters\Products;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\Products\Resources\BrandResource\Pages;
use App\Filament\Clusters\Products\Resources\BrandResource\RelationManagers;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $cluster = Products::class;

    protected static ?string $navigationParentItem = 'Products';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur:true)
                                ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set ) => $operation === 'create' ? $set('slug', Str::slug($state)) : NULL)
                                ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set ) => $operation === 'edit' ? $set('slug', Str::slug($state)) : NULL),

                                Forms\Components\TextInput::make('slug')
                                ->required()
                                ->maxLength(255)
                                ->disabled()
                                ->dehydrated()
                                ->unique(Brand::class, 'slug', ignoreRecord:true)

                            ]),
                        Forms\Components\Toggle::make('is_visible')
                        ->label('Visible to customer')
                        ->default(true),
            
                        Forms\Components\MarkDownEditor::make('description')
                        ->label('Description')
              
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),

                Tables\Columns\IconColumn::make('is_visible')
                ->label('Visibility')
                ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                ->label('Updated Date')
                ->date()
                ->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
