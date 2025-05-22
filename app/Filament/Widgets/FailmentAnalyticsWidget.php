<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\Platform;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class FailmentAnalyticsWidget extends Widget
{
    protected static string $view = 'filament.widgets.failment-analytics-widget';
    
    // Make widget only appear on the post-analytics page
    public static function canView(): bool
    {
        return request()->routeIs('filament.pages.post-analytics');
    }
    
    protected function getViewData(): array
    {
        // Get failure statistics by platform
        $platformFailures = Platform::select(
                'platforms.id',
                'platforms.name',
                'platforms.type',
                DB::raw('COUNT(post_platform.platform_status) as total_posts'),
                DB::raw('SUM(CASE WHEN post_platform.platform_status = "failed" THEN 1 ELSE 0 END) as failed_posts')
            )
            ->leftJoin('post_platform', 'platforms.id', '=', 'post_platform.platform_id')
            ->groupBy('platforms.id', 'platforms.name', 'platforms.type')
            ->get()
            ->map(function ($platform) {
                $failureRate = $platform->total_posts > 0
                    ? round(($platform->failed_posts / $platform->total_posts) * 100, 1)
                    : 0;
                
                return [
                    'id' => $platform->id,
                    'name' => $platform->name,
                    'type' => $platform->type,
                    'total_posts' => $platform->total_posts,
                    'failed_posts' => $platform->failed_posts,
                    'failure_rate' => $failureRate,
                ];
            })
            ->sortByDesc('failure_rate');
        
        // Get most common failure reasons
        $commonFailures = DB::table('post_platform')
            ->select('failure_reason', DB::raw('COUNT(*) as count'))
            ->whereNotNull('failure_reason')
            ->where('platform_status', 'failed')
            ->groupBy('failure_reason')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
        
        return [
            'platformFailures' => $platformFailures,
            'commonFailures' => $commonFailures,
        ];
    }
} 