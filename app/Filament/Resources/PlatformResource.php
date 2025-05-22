<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlatformResource\Pages;
use App\Filament\Resources\PlatformResource\RelationManagers;
use App\Models\Platform;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlatformResource extends Resource
{
    protected static ?string $model = Platform::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    
    protected static ?string $navigationGroup = 'Content';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\Select::make('type')
                    ->options([
                        'twitter' => 'Twitter',
                        'instagram' => 'Instagram',
                        'linkedin' => 'LinkedIn',
                        'facebook' => 'Facebook',
                        'tiktok' => 'TikTok',
                        'youtube' => 'YouTube',
                        'pinterest' => 'Pinterest',
                        'reddit' => 'Reddit',
                    ])
                    ->required(),
                    
                Forms\Components\Textarea::make('description')
                    ->nullable()
                    ->columnSpanFull(),
                    
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'twitter' => 'info',
                        'instagram' => 'danger',
                        'linkedin' => 'primary',
                        'facebook' => 'primary',
                        'tiktok' => 'gray',
                        'youtube' => 'danger',
                        'pinterest' => 'danger',
                        'reddit' => 'warning',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                    
                Tables\Columns\TextColumn::make('posts_count')
                    ->counts('posts')
                    ->label('Posts')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'twitter' => 'Twitter',
                        'instagram' => 'Instagram',
                        'linkedin' => 'LinkedIn',
                        'facebook' => 'Facebook',
                        'tiktok' => 'TikTok',
                        'youtube' => 'YouTube',
                        'pinterest' => 'Pinterest',
                        'reddit' => 'Reddit',
                    ]),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All platforms')
                    ->trueLabel('Active platforms')
                    ->falseLabel('Inactive platforms'),
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
            'index' => Pages\ListPlatforms::route('/'),
            'create' => Pages\CreatePlatform::route('/create'),
            'edit' => Pages\EditPlatform::route('/{record}/edit'),
        ];
    }
}
