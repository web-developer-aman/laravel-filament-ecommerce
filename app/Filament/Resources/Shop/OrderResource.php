<?php

namespace App\Filament\Resources\Shop;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Enums\OrderStatus;
use App\Models\Shop\Order;
use Filament\Tables\Table;
use Squire\Models\Currency;
use App\Models\Shop\Product;
use Filament\Resources\Resource;
use App\Models\Shop\ProductVariation;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Shop\OrderResource\Pages;
use App\Filament\Clusters\Products\Resources\ProductResource;
use App\Filament\Resources\Shop\OrderResource\RelationManagers;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static?string $recordTitleAttribute = 'name';

    protected static?string $navigationGroup = 'Shop';

    protected static?int $navigationSort = 2;

    protected static?string $slug ='shop/orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema(static::getDetailsFormSchema())
                            ->columns(2),

                        Forms\Components\Section::make('Order Items')
                            ->headerActions([
                                Action::make('reset')
                                    ->modalHeading('Are you sure?')
                                    ->modalDescription('All existing item will be removed from the order')
                                    ->requiresConfirmation()
                                    ->color('danger')
                                    ->action(fn (Forms\Set $set) => $set('items' , []))
                            ])
                            ->schema([
                                static::getItemsRepeater()
                            ]),
                        Forms\Components\Section::make()
                            ->schema(static::getPrice())
                            
                    ])
                    ->columnSpan(['lg' => fn (?Order $record) => $record === null ? 3 : 2])
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('currency'),

                Tables\Columns\TextColumn::make('total_price'),

                Tables\Columns\TextColumn::make('shipping_price'),

                Tables\Columns\TextColumn::make('created_at')
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getDetailsFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('number')
                ->disabled()
                ->default('OR-' . random_int(100000, 999999))
                ->dehydrated()
                ->required()
                ->maxLength(32)
                ->unique(Order::class, 'number', ignoreRecord: true),

            Forms\Components\Select::make('shop_customer_id')
                ->relationship('customer', 'name')
                ->required()
                ->searchable()
                ->createOptionForm([
                    Forms\Components\TextInput::make('name'),

                    Forms\Components\TextInput::make('email'),

                    Forms\Components\TextInput::make('phone'),

                    Forms\Components\Select::make('gender')
                        ->placeholder('Select gender')
                        ->options([
                            'male' => 'Male',
                            'female' => 'Female',
                            'other' => 'Other',
                        ])
                        ->required()
                        ->native(false),

                ])
                ->createOptionAction(function (Action $action){
                    return $action
                        ->modalHeading('Create Customer')
                        ->modalSubmitActionLabel('Create Customer')
                        ->modalWidth('lg');
                }),
            
            Forms\Components\ToggleButtons::make('status')
                ->inline()
                ->required()
                ->options(OrderStatus::class),
            
            Forms\Components\Select::make('currency')
                ->searchable()
                ->required()
                ->getSearchResultsUsing(fn (String $query) => Currency::where('name', 'like', "%{$query}%")
                ->pluck('name','id'))
                ->getOptionLabelUsing(fn ($value): ?string => Currency::firstWhere('id', $value)?->getAttribute('name')),

            Forms\Components\MarkDownEditor::make('notes')
                ->columnSpan('full')
        ];
    }

    public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('items')
            ->relationship()
            ->schema([
                Forms\Components\Select::make('shop_product_id')
                    ->label('Product')
                    ->options(Product::query()->pluck('name', 'id'))
                    ->required()
                    ->reactive()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if (Product::find($state) && Product::find($state)->variations()->count() == 0) { 
                            $set('unit_price', Product::find($state)?->price?? 0);  
                        }else{
                            $set('unit_price', null);
                        }

                        $set('shop_variation_id', null);
                        
                    })
                    
                    ->distinct()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->columnSpan([
                        'md' => 5,
                    ])
                    ->searchable(),
                
                    Forms\Components\Select::make('shop_variation_id')
                    ->label('Variation')
                    ->options(fn (Forms\Get $get) => (
                        ProductVariation::query()
                            ->where('shop_product_id', $get('shop_product_id'))
                            ->pluck('name', 'id')
                            
                    ))
                    
                    ->live()
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('unit_price', ProductVariation::find($state)?->price ?? 0))
                    ->searchable()
                    ->preload()
                    ->columnSpan([
                        'md' => 5,
                    ]),
                
                    
                Forms\Components\TextInput::make('qty')
                    ->label('Quantity')
                    ->numeric()
                    ->default(1)
                    ->columnSpan([
                        'md' => 2
                    ]),

                Forms\Components\TextInput::make('unit_price')
                    ->label('Unit price')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->required()
                    ->columnSpan([
                        'md' => 3
                    ])
            ])
            ->live(true)
            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                        self::updateTotals($get, $set);
            })
            ->extraItemActions([
                Action::make('openProduct')
                    ->tooltip('Open product')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(function (array $arguments, Repeater $component): ?string {
                        $itemData = $component->getRawItemState($arguments['item']);

                        $product = Product::find($itemData['shop_product_id']);

                        if (! $product) {
                            return null;
                        }

                        return ProductResource::getUrl('edit', ['record' => $product]);
                    }, shouldOpenInNewTab: true)
                    ->hidden(fn (array $arguments, Repeater $component): bool => blank($component->getRawItemState($arguments['item'])['shop_product_id'])),
            ])
            ->orderColumn('sort')
            ->defaultItems(1)
            ->hiddenLabel()
            ->columns([
                'md' => 10,
            ])
            ->required();
    }

    public static function getPrice(): array 
    {
        return [
            Forms\Components\Section::make()
            ->columns(2)
            ->maxWidth('1/2')
            ->schema([
                Forms\Components\Select::make('shipping_method')
                ->placeholder('Select shipping method')
                ->options([
                    'percentage' => 'Percentage',
                    'fixed' => 'Fixed Rate',
                ])
                // Live field, as we need to re-calculate the total on each change
                ->live(true)
                // This enables us to display the subtotal on the edit page load
                ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                    self::updateTotals($get, $set);
                }),
                Forms\Components\TextInput::make('shipping_price')
                ->required()
                ->numeric()
                ->default(20)
                // Live field, as we need to re-calculate the total on each change
                ->live(true)
                // This enables us to display the subtotal on the edit page load
                ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                    self::updateTotals($get, $set);
                }),
                Forms\Components\TextInput::make('total_price')
                    ->label('Total price')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->required()
                    // Read-only, because it's calculated
                    ->prefix('$')
                    ->afterStateHydrated(function (Forms\Get $get, Forms\Set $set) {
                        self::updateTotals($get, $set);
                })
                ->columnSpan(2),
            ])
        ];
    }

    // This function updates totals based on the selected products and quantities
    public static function updateTotals(Forms\Get $get, Forms\Set $set): void
    {
        // Retrieve all selected products and remove empty rows
        $selectedProducts = collect($get('items'))->filter(fn($item) => !empty($item['shop_product_id']) && !empty($item['qty']));
    
        // Retrieve prices for all selected products
        $prices = Product::find($selectedProducts->pluck('shop_product_id'))->pluck('price', 'id');
    
        // Calculate subtotal based on the selected products and quantities
        $subtotal = $selectedProducts->reduce(function ($subtotal, $product) use ($prices) {
            return $subtotal + ($prices[$product['shop_product_id']] * $product['qty']);
        }, 0);

        if($get('shipping_method') === 'fixed'){
            $shippingPrice = $get('shipping_price');
        }else{
            $shippingPrice = ($subtotal * ($get('shipping_price') / 100));
        }
    
        // Update the state with the new values
        $set('subtotal', number_format($subtotal, 2, '.', ''));
        $set('total_price', number_format($subtotal + $shippingPrice, 2, '.', ''));
    }
}
