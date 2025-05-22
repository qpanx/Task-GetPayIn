<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\FailmentAnalyticsWidget;
use Filament\Pages\Page;
use Filament\Support\Facades\FilamentIcon;

class PostAnalytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    
    protected static string $view = 'filament.pages.post-analytics';
    
    protected static ?string $navigationLabel = 'Post Analytics';
    
    protected static ?int $navigationSort = 3;
    
    protected static ?string $navigationGroup = 'Analytics';
    
    public function getWidgets(): array
    {
        return [
            FailmentAnalyticsWidget::class,
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return 'NEW';
    }
} 