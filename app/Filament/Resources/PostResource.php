<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Blog Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Post Content')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                                        $operation === 'create' ? $set('slug', \Str::slug($state)) : null),
                                
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                
                                Forms\Components\MarkdownEditor::make('content')
                                    ->required()
                                    ->columnSpanFull(),
                                
                                Forms\Components\Textarea::make('summary')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                            
                        Forms\Components\Section::make('Categories and Tags')
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload(),
                                
                                Forms\Components\Select::make('tags')
                                    ->relationship('tags', 'name')
                                    ->multiple()
                                    ->preload(),
                            ]),
                    ])
                    ->columnSpan(2),
                
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status & Language')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                        'deleted' => 'Deleted',
                                    ])
                                    ->default('draft')
                                    ->required(),
                                
                                Forms\Components\Select::make('locale')
                                    ->label('Language')
                                    ->options([
                                        'en' => 'English',
                                        'zh' => '繁體中文',
                                        'zh-CN' => '简体中文',
                                        'ja' => '日本語',
                                        'vi' => 'Tiếng Việt',
                                    ])
                                    ->default('en')
                                    ->required(),
                                
                                Forms\Components\Select::make('original_post_id')
                                    ->label('Original Post (if this is a translation)')
                                    ->relationship('originalPost', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->options(function (callable $get, ?Post $record) {
                                        // Exclude current post and posts that are translations
                                        return Post::where('id', '!=', $record?->id ?? 0)
                                            ->whereNull('original_post_id')
                                            ->pluck('title', 'id')
                                            ->toArray();
                                    }),
                                
                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Created at')
                                    ->content(fn (?Post $record): string => $record?->created_at?->diffForHumans() ?? 'Not created yet'),
                                
                                Forms\Components\Placeholder::make('updated_at')
                                    ->label('Last modified at')
                                    ->content(fn (?Post $record): string => $record?->updated_at?->diffForHumans() ?? 'Not modified yet'),
                            ]),
                            
                        Forms\Components\Section::make('Featured Image')
                            ->schema([
                                Forms\Components\FileUpload::make('featured_image')
                                    ->directory('posts/featured-images')
                                    ->image()
                                    ->imageEditor(),
                            ]),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('locale')
                    ->label('Language')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'en' => 'English',
                        'zh' => '繁體中文',
                        'zh-CN' => '简体中文',
                        'ja' => '日本語',
                        'vi' => 'Tiếng Việt',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'en' => 'info',
                        'zh' => 'success',
                        'zh-CN' => 'success',
                        'ja' => 'warning',
                        'vi' => 'danger',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'deleted' => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('views')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('originalPost.title')
                    ->label('Translation of')
                    ->default('-')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'deleted' => 'Deleted',
                    ]),
                
                Tables\Filters\SelectFilter::make('locale')
                    ->label('Language')
                    ->options([
                        'en' => 'English',
                        'zh' => '繁體中文',
                        'zh-CN' => '简体中文',
                        'ja' => '日本語',
                        'vi' => 'Tiếng Việt',
                    ]),
                
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                    
                Tables\Filters\Filter::make('translations')
                    ->label('Show only translations')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('original_post_id')),

                Tables\Filters\Filter::make('original_posts')
                    ->label('Show only original posts')
                    ->query(fn (Builder $query): Builder => $query->whereNull('original_post_id')),
                
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('viewOnWebsite')
                    ->label('View')
                    ->icon('heroicon-o-globe-alt')
                    ->color('success')
                    ->url(fn (Post $record) => route('blog.show', $record->slug))
                    ->openUrlInNewTab()
                    ->visible(fn (Post $record) => $record->status === 'published'),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Relations will be added later
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
