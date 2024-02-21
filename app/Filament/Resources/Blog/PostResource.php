<?php

namespace App\Filament\Resources\Blog;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Blog\Post;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\SpatieTagsInput;
use App\Filament\Resources\Blog\PostResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Blog\PostResource\RelationManagers;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static?string $recordTitleAttribute = 'title';

    protected static?string $navigationGroup = 'Blog';

    public static?int $navigationSort = 0;

    protected static?string $slug = 'blog/posts';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength('255')
                        ->live(onBlur:true)
                        ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create'? $set('slug', Str::slug($state)) : null)
                        ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'edit'? $set('slug', Str::slug($state)) : null),
        
                        Forms\Components\TextInput::make('slug')
                        ->required()
                        ->disabled()
                        ->dehydrated()
                        ->maxLength('255')
                        ->unique(Post::class,'slug', ignoreRecord: true),
        
                        Forms\Components\MarkDownEditor::make('content')
                        ->columnSpan('full'),
        
                        Forms\Components\Select::make('blog_author_id')
                        ->relationship('author', 'name')
                        ->required()
                        ->searchable(),
        
                        Forms\Components\Select::make('blog_category_id')
                        ->relationship('category', 'name')
                        ->required()
                        ->searchable(),
        
                        Forms\Components\DatePicker::make('published_at')
                        ->label('Published At'),
        
                        SpatieTagsInput::make('tags'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Image')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                        ->image()
                        ->hiddenLabel()
                    ])
                    ->collapsible(),
                ]);
            
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                ->label('Image'),

                Tables\Columns\TextColumn::make('title')
                ->searchable()
                ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                ->searchable()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('author.name')
                ->searchable()
                ->sortable()
                ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                ->badge()
                ->getStateUsing(fn (Post $records): string => $records->published_at?->isPast() ? 'Published' : 'Draft')
                ->colors([
                    'success' => 'Published',
                    'warning' => 'Draft',
                ]),
                Tables\Columns\TextColumn::make('category.name')
                ->searchable()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('published_at')
                ->date()
                ->label('Published Date')
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
