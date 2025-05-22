<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\ActivityLog;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Content';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Post Content')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\RichEditor::make('content')
                            ->required()
                            ->columnSpanFull(),
                            
                        Forms\Components\FileUpload::make('image_url')
                            ->image()
                            ->directory('post-images')
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
                    
                Forms\Components\Section::make('Publishing Settings')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                Post::STATUS_DRAFT => 'Draft',
                                Post::STATUS_SCHEDULED => 'Scheduled',
                            ])
                            ->default(Post::STATUS_DRAFT)
                            ->required(),
                            
                        Forms\Components\DateTimePicker::make('scheduled_time')
                            ->required()
                            ->visible(fn (Forms\Get $get) => $get('status') === Post::STATUS_SCHEDULED),
                            
                        Forms\Components\Select::make('platforms')
                            ->relationship('platforms', 'name')
                            ->multiple()
                            ->preload()
                            ->required(),
                    ]),
                    
                Forms\Components\Hidden::make('user_id')
                    ->default(fn () => Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Post::STATUS_DRAFT => 'gray',
                        Post::STATUS_SCHEDULED => 'warning',
                        Post::STATUS_PUBLISHED => 'success',
                        Post::STATUS_PARTIAL => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('scheduled_time')
                    ->dateTime()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('platforms.name')
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        Post::STATUS_DRAFT => 'Draft',
                        Post::STATUS_SCHEDULED => 'Scheduled',
                        Post::STATUS_PUBLISHED => 'Published',
                        Post::STATUS_PARTIAL => 'Partially Published',
                    ]),
                    
                Tables\Filters\Filter::make('scheduled_time')
                    ->form([
                        Forms\Components\DatePicker::make('scheduled_from'),
                        Forms\Components\DatePicker::make('scheduled_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['scheduled_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_time', '>=', $date),
                            )
                            ->when(
                                $data['scheduled_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_time', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('publish')
                    ->label('Publish Now')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn (Post $record) => $record->status !== Post::STATUS_PUBLISHED)
                    ->action(function (Post $record) {
                        // In a real app, you'd call a service to publish the post
                        $record->status = Post::STATUS_PUBLISHED;
                        $record->save();
                        
                        // Log the activity
                        ActivityLog::log(
                            Auth::id(),
                            'publish',
                            'post',
                            $record->id,
                            ['platforms' => $record->platforms->pluck('name')->toArray()]
                        );
                    }),
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
