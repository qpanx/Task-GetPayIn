<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\PostPublishingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish posts that are scheduled for publication';

    /**
     * The post publishing service.
     *
     * @var \App\Services\PostPublishingService
     */
    protected $publishingService;

    /**
     * Create a new command instance.
     *
     * @param  \App\Services\PostPublishingService  $publishingService
     * @return void
     */
    public function __construct(PostPublishingService $publishingService)
    {
        parent::__construct();
        $this->publishingService = $publishingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $posts = Post::dueForPublishing()->with('platforms')->get();
        
        if ($posts->isEmpty()) {
            $this->info('No posts are due for publishing.');
            return 0;
        }
        
        $this->info("Found {$posts->count()} posts to publish.");
        
        foreach ($posts as $post) {
            $this->publishPost($post);
        }
        
        return 0;
    }
    
    /**
     * Publish the given post.
     */
    protected function publishPost(Post $post)
    {
        $this->info("Publishing post: {$post->title}");
        
        try {
            $allSuccess = true;
            
            // Try to publish to all platforms
            foreach ($post->platforms as $platform) {
                $this->info("Publishing to {$platform->name}...");
                
                $result = $this->publishingService->publishToPlatform($post, $platform);
                
                if ($result['success']) {
                    $this->info("Success: {$result['message']}");
                    
                    $post->platforms()->updateExistingPivot(
                        $platform->id, 
                        [
                            'platform_status' => 'published',
                            'failure_reason' => null
                        ]
                    );
                } else {
                    $this->error("Failed: {$result['message']}");
                    
                    $post->platforms()->updateExistingPivot(
                        $platform->id, 
                        [
                            'platform_status' => 'failed',
                            'failure_reason' => $result['message']
                        ]
                    );
                    
                    $allSuccess = false;
                }
            }
            
            // Update post status based on overall success
            $post->status = $allSuccess ? 'published' : 'partial';
            $post->save();
            
            $this->info("Post ID: {$post->id} processed. Status: {$post->status}");
            Log::info("Post {$post->id} published with status: {$post->status}");
            
        } catch (\Exception $e) {
            $this->error("Failed to process post ID: {$post->id}. Error: {$e->getMessage()}");
            Log::error("Failed to process post {$post->id}: {$e->getMessage()}");
        }
    }
} 