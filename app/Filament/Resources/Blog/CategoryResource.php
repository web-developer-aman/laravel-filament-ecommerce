<?php

namespace App\Filament\Resources\Blog;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\Blog\Category;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Blog\CategoryResource\Pages;
use App\Filament\Resources\Blog\CategoryResource\RelationManagers;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static?string $recordTitleAttribute = 'name';

    protected static?string $navigationGroup = 'Blog';

    public static?int $navigationSort = 1;

    protected static?string $slug = 'blog/categories';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength('255')
                ->live(onBlur:true)
                ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'edit' ? $set('slug', Str::slug($state)) : null),
                
                Forms\Components\TextInput::make('slug')
                ->required()
                ->disabled()
                ->dehydrated()
                ->maxLength('255')
                ->unique(Category::class,'slug', ignoreRecord: true),

                Forms\Components\MarkDownEditor::make('description')
                ->columnSpan('full')
                ->label('Description'),

                Forms\Components\Toggle::make('is_visible')
                ->label('Visible to customers')
                ->default(true),
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
                Tables\Columns\TextColumn::make('slug')
                ->searchable()
                ->sortable(),
                Tables\Columns\IconColumn::make('is_visible')
                ->label('Visiblity'),
                Tables\Columns\TextColumn::make('updated_at')
                ->label('Last Updated')
                ->date(),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            // 'create' => Pages\CreateCategory::route('/create'),
            // 'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
