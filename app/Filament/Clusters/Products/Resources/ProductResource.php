<?php

namespace App\Filament\Clusters\Products\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\Shop\Product;
use Filament\Resources\Resource;
use App\Filament\Clusters\Products;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Clusters\Products\Resources\ProductResource\Pages;
use App\Filament\Clusters\Products\Resources\ProductResource\RelationManagers;
use App\Filament\Clusters\Products\Resources\ProductResource\Widgets\ProductStats;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $cluster = Products::class;

    protected static ?string $navigationLebel = 'Products';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur:true)
                                ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : NULL)
                                ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'edit' ? $set('slug', Str::slug($state)) : NULL),

                                Forms\Components\TextInput::make('slug')
                                ->required()
                                ->disabled()
                                ->dehydrated()
                                ->maxLength(255)
                                ->unique(Product::class, 'slug', ignoreRecord:true),

                                Forms\Components\MarkDownEditor::make('description')
                                ->columnSpan('full')
                            ])->columns(2),

                        Forms\Components\Section::make('Images')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('media')
                                ->collection('product-images')
                                ->maxFiles(5)
                                ->multiple()
                                ->hiddenLabel()
                            ])
                            ->collapsible(),
                        
                        Forms\Components\Section::make('Pricing')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                ->required()
                                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                ->numeric(),

                                Forms\Components\TextInput::make('old_price')
                                ->label('Compare at price')
                                ->required()
                                ->numeric()
                                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),

                                Forms\Components\TextInput::make('cost')
                                ->label('Cost per item')
                                ->required()
                                ->numeric()
                                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                ->helperText('Customer won\'t see this price')
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Inventory')
                            ->schema([
                                Forms\Components\TextInput::make('sku')
                                ->required()
                                ->maxLength(255)
                                ->label('SKU (Stock Keeping Unit)')
                                ->unique(Product::class, 'sku', ignoreRecord:true),

                                Forms\Components\TextInput::make('barcode')
                                ->required()
                                ->maxLength(255)
                                ->label('Barcode (ISBN, UPC, GTIN, etc.)')
                                ->unique(Product::class, 'barcode', ignoreRecord:true),

                                Forms\Components\TextInput::make('qty')
                                ->label('Quantity')
                                ->required()
                                ->numeric()
                                ->rules(['integer', 'min:0']),

                                Forms\Components\TextInput::make('security_stock')
                                ->required()
                                ->numeric()
                                ->rules(['integer', 'min:0'])
                                ->helperText('The safety stock is the limit stock for your products which alerts you if the product stock will soon be out of stock.')
                            ])
                            ->columns(2),
                        
                        Forms\Components\Section::make('Shipping')
                            ->schema([
                                Forms\Components\Checkbox::make('backorder')
                                ->label('This product can be returned'),

                                Forms\Components\Checkbox::make('requires_shipping')
                                ->label('This product can be shipped')
                            ])
                            ->columns(2)
                    ])
                    ->columnSpan(['lg' => 2]),
                
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status')
                            ->schema([
                                Forms\Components\Toggle::make('is_visible')
                                ->label('Visible')
                                ->default(true)
                                ->helperText('This product will be hidden from all sales channels'),

                                Forms\Components\DatePicker::make('published_at')
                                ->label('Availability')
                                ->required()
                                ->default(now())
                            ]),

                        Forms\Components\Section::make('Associations')
                            ->schema([
                                Forms\Components\Select::make('shop_brand_id')
                                ->relationship('brand', 'name')
                                ->searchable()
                                ->hiddenOn(ProductsRelationManager::class),

                                Forms\Components\Select::make('categories')
                                ->relationship('categories', 'name')
                                ->multiple()
                                ->required()
                            ])
                    ])
            ]
            )->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('product-image')
                ->label('Image')
                ->collection('product-images'),

                Tables\Columns\TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),

                Tables\Columns\TextColumn::make('brand.name')
                ->searchable()
                ->sortable()
                ->toggleable(),

                Tables\Columns\IconColumn::make('is_visible')
                ->label('Visibility')
                ->sortable()
                ->toggleable(),

                Tables\Columns\TextColumn::make('price')
                ->label('Price')
                ->sortable()
                ->searchable(),

                Tables\Columns\TextColumn::make('sku')
                ->label('SKU')
                ->searchable()
                ->sortable()
                ->toggleable(),

                Tables\Columns\TextColumn::make('qty')
                ->label('Quantity')
                ->searchable()
                ->sortable()
                ->toggleable(),

                Tables\Columns\TextColumn::make('security_stock')
                ->searchable()
                ->sortable()
                ->toggleable()
                ->toggledHiddenByDefault(),

                Tables\Columns\TextColumn::make('published_at')
                ->label('Publish Date')
                ->date()
                ->sortable()
                ->toggleable()
                ->toggledHiddenByDefault()
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
             RelationManagers\VariationsRelationManager::class
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ProductStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
