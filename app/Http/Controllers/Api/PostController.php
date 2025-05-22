<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Post;
use App\Models\Platform;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = User::find(Auth::id());
        $query = $user->posts();
        
        // Apply status filter if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Apply date filter if provided
        if ($request->has('date')) {
            $query->whereDate('scheduled_time', $request->date);
        }
        
        // Get posts with platforms
        $posts = $query->with('platforms')->latest()->paginate(10);
        
        return response()->json($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if user has reached daily post limit
        $user = User::find(Auth::id());
        if ($user->hasReachedDailyPostLimit() && $request->status !== 'draft') {
            return response()->json([
                'message' => 'You have reached the daily limit of 10 scheduled posts.'
            ], 422);
        }
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image_url' => 'nullable|string',
            'scheduled_time' => 'required|date|after_or_equal:now',
            'status' => 'required|in:draft,scheduled,published',
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'exists:platforms,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validate content length for each platform
        $platforms = Platform::whereIn('id', $request->platforms)->get();
        foreach ($platforms as $platform) {
            $limit = $platform->getCharacterLimit();
            if (strlen($request->content) > $limit) {
                return response()->json([
                    'message' => "Content exceeds {$platform->name}'s character limit of {$limit}."
                ], 422);
            }
            
            // Check if image is required but not provided
            if ($platform->requirements['image_required'] ?? false) {
                if (empty($request->image_url)) {
                    return response()->json([
                        'message' => "{$platform->name} requires an image."
                    ], 422);
                }
            }
        }
        
        // Create post
        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'image_url' => $request->image_url,
            'scheduled_time' => $request->scheduled_time,
            'status' => $request->status,
            'user_id' => Auth::id(),
        ]);
        
        // Attach platforms
        foreach ($request->platforms as $platformId) {
            $post->platforms()->attach($platformId, ['platform_status' => 'pending']);
        }
        
        // Log activity
        ActivityLog::log(
            Auth::id(),
            'create',
            'post',
            $post->id,
            ['platforms' => $request->platforms]
        );
        
        return response()->json($post->load('platforms'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find(Auth::id());
        $post = $user->posts()->with('platforms')->findOrFail($id);
        
        return response()->json($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find(Auth::id());
        $post = $user->posts()->findOrFail($id);
        
        // Only draft or scheduled posts can be updated
        if ($post->status === 'published') {
            return response()->json([
                'message' => 'Published posts cannot be updated.'
            ], 422);
        }
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'image_url' => 'nullable|string',
            'scheduled_time' => 'sometimes|date|after_or_equal:now',
            'status' => 'sometimes|in:draft,scheduled,published',
            'platforms' => 'sometimes|array|min:1',
            'platforms.*' => 'exists:platforms,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Check daily limit if changing status to scheduled
        if (($request->status === 'scheduled' && $post->status === 'draft') && $user->hasReachedDailyPostLimit()) {
            return response()->json([
                'message' => 'You have reached the daily limit of 10 scheduled posts.'
            ], 422);
        }
        
        // Validate content length for each platform if content is being updated
        if ($request->has('content')) {
            $platformIds = $request->has('platforms') ? $request->platforms : $post->platforms->pluck('id')->toArray();
            $platforms = Platform::whereIn('id', $platformIds)->get();
            
            foreach ($platforms as $platform) {
                $limit = $platform->getCharacterLimit();
                if (strlen($request->content) > $limit) {
                    return response()->json([
                        'message' => "Content exceeds {$platform->name}'s character limit of {$limit}."
                    ], 422);
                }
            }
        }
        
        // Update post
        $post->update($request->only([
            'title', 'content', 'image_url', 'scheduled_time', 'status'
        ]));
        
        // Update platforms if provided
        if ($request->has('platforms')) {
            $post->platforms()->sync(collect($request->platforms)->mapWithKeys(function ($platformId) {
                return [$platformId => ['platform_status' => 'pending']];
            }));
        }
        
        // Log activity
        ActivityLog::log(
            Auth::id(),
            'update',
            'post',
            $post->id,
            $request->all()
        );
        
        return response()->json($post->load('platforms'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find(Auth::id());
        $post = $user->posts()->findOrFail($id);
        
        // Log activity
        ActivityLog::log(
            Auth::id(),
            'delete',
            'post',
            $post->id,
            ['title' => $post->title]
        );
        
        $post->delete();
        
        return response()->json([
            'message' => 'Post deleted successfully'
        ]);
    }
}
