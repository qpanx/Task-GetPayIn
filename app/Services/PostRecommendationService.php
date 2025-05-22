<?php

namespace App\Services;

use App\Models\Platform;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PostRecommendationService
{
    /**
     * Get recommended platforms based on past performance.
     *
     * @return Collection
     */
    public function getRecommendedPlatforms(): Collection
    {
        // Get platforms with their success rates
        return Platform::select(
                'platforms.id',
                'platforms.name', 
                'platforms.type',
                DB::raw('COUNT(platform_post.platform_status) as total_attempts'),
                DB::raw('SUM(CASE WHEN platform_post.platform_status = "published" THEN 1 ELSE 0 END) as successful')
            )
            ->leftJoin('platform_post', 'platforms.id', '=', 'platform_post.platform_id')
            ->whereNotNull('platform_post.platform_status')
            ->groupBy('platforms.id', 'platforms.name', 'platforms.type')
            ->get()
            ->map(function ($platform) {
                $successRate = $platform->total_attempts > 0
                    ? round(($platform->successful / $platform->total_attempts) * 100, 1)
                    : 0;
                
                return [
                    'id' => $platform->id,
                    'name' => $platform->name,
                    'type' => $platform->type,
                    'success_rate' => $successRate,
                    'recommendation' => $this->getPlatformRecommendation($successRate),
                    'total_posts' => $platform->total_attempts,
                ];
            })
            ->sortByDesc('success_rate');
    }
    
    /**
     * Get recommended posting times based on successful posts.
     *
     * @return array
     */
    public function getRecommendedTimes(): array
    {
        // Get hours with most successful posts
        $hourlySuccess = Post::where('status', Post::STATUS_PUBLISHED)
            ->selectRaw('HOUR(scheduled_time) as hour, COUNT(*) as count')
            ->groupBy(DB::raw('HOUR(scheduled_time)'))
            ->orderByDesc('count')
            ->limit(5)
            ->get();
            
        // Get days of week with most successful posts
        $dailySuccess = Post::where('status', Post::STATUS_PUBLISHED)
            ->selectRaw('DAYOFWEEK(scheduled_time) as day, COUNT(*) as count')
            ->groupBy(DB::raw('DAYOFWEEK(scheduled_time)'))
            ->orderByDesc('count')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                // Convert 1-7 (Sunday-Saturday) to day name
                $day = Carbon::create(2023, 1, 1)->startOfWeek()->addDays($item->day - 1);
                return [
                    'day' => $day->format('l'),
                    'count' => $item->count,
                ];
            });
        
        return [
            'best_hours' => $hourlySuccess->map(function ($item) {
                $hour = sprintf('%02d:00', $item->hour);
                return [
                    'time' => $hour,
                    'formatted' => Carbon::createFromFormat('H:i', $hour)->format('g:i A'),
                    'count' => $item->count,
                ];
            }),
            'best_days' => $dailySuccess,
        ];
    }
    
    /**
     * Get platform-specific recommendations.
     *
     * @param Platform $platform
     * @return array
     */
    public function getPlatformSpecificTimes(Platform $platform): array
    {
        $bestTimes = DB::table('posts')
            ->join('platform_post', 'posts.id', '=', 'platform_post.post_id')
            ->where('platform_post.platform_id', $platform->id)
            ->where('platform_post.platform_status', 'published')
            ->selectRaw('HOUR(posts.scheduled_time) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderByDesc('count')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                $hour = sprintf('%02d:00', $item->hour);
                return [
                    'time' => $hour,
                    'formatted' => Carbon::createFromFormat('H:i', $hour)->format('g:i A'),
                    'count' => $item->count,
                ];
            });
            
        return [
            'platform' => $platform->name,
            'best_times' => $bestTimes,
        ];
    }
    
    /**
     * Get a recommendation string based on success rate.
     *
     * @param float $successRate
     * @return string
     */
    protected function getPlatformRecommendation(float $successRate): string
    {
        if ($successRate >= 90) {
            return 'Highly Recommended';
        } elseif ($successRate >= 70) {
            return 'Recommended';
        } elseif ($successRate >= 50) {
            return 'Average Results';
        } elseif ($successRate >= 30) {
            return 'Use with Caution';
        } else {
            return 'Not Recommended';
        }
    }
    
    /**
     * Generate a complete recommendation for a new post.
     *
     * @return array
     */
    public function generatePostingRecommendation(): array
    {
        $platforms = $this->getRecommendedPlatforms();
        $times = $this->getRecommendedTimes();
        
        // Most successful platform
        $topPlatform = $platforms->first();
        
        // Best day and time
        $bestDay = $times['best_days']->first()['day'] ?? 'N/A';
        $bestTime = $times['best_hours']->first()['formatted'] ?? 'N/A';
        
        return [
            'summary' => "Based on your post history, we recommend posting on {$bestDay} at {$bestTime} to {$topPlatform['name']} for the best results.",
            'platforms' => $platforms,
            'times' => $times,
        ];
    }
} 