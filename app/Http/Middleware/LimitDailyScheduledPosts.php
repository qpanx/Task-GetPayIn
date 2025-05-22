<?php

namespace App\Http\Middleware;

use App\Models\Post;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LimitDailyScheduledPosts
{
    /**
     * Maximum number of scheduled posts allowed per day.
     */
    protected const MAX_DAILY_POSTS = 10;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Skip check if not logged in or not a post creation/update request
        if (!$user || (!$request->isMethod('post') && !$request->isMethod('put'))) {
            return $next($request);
        }
        
        // Only apply limit when scheduling posts
        if (!$request->has('status') || $request->input('status') !== Post::STATUS_SCHEDULED) {
            return $next($request);
        }
        
        // Count how many posts the user has already scheduled today
        $todayCount = Post::where('user_id', $user->id)
            ->where('status', Post::STATUS_SCHEDULED)
            ->whereDate('created_at', now()->toDateString())
            ->count();
        
        // For updates, don't count the current post
        if ($request->isMethod('put') && $request->route('post')) {
            $postId = $request->route('post');
            $existingPost = Post::find($postId);
            
            if ($existingPost && 
                $existingPost->user_id === $user->id && 
                $existingPost->status === Post::STATUS_SCHEDULED && 
                $existingPost->created_at->toDateString() === now()->toDateString()) {
                $todayCount--;
            }
        }
        
        if ($todayCount >= self::MAX_DAILY_POSTS) {
            return back()
                ->withInput()
                ->withErrors(['limit' => 'You have reached the maximum limit of ' . self::MAX_DAILY_POSTS . ' scheduled posts per day.']);
        }
        
        return $next($request);
    }
} 