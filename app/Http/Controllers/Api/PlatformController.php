<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlatformController extends Controller
{
    /**
     * Display a listing of all available platforms.
     */
    public function index()
    {
        $platforms = Platform::all();
        return response()->json($platforms);
    }

    /**
     * Get user's active platforms.
     */
    public function userPlatforms()
    {
        $platforms = Auth::user()->platforms;
        return response()->json($platforms);
    }

    /**
     * Toggle platform active status for the authenticated user.
     */
    public function togglePlatform(Request $request, string $id)
    {
        $platform = Platform::findOrFail($id);
        $user = Auth::user();
        
        // Check if user already has this platform
        $exists = $user->platforms()->where('platform_id', $platform->id)->exists();
        
        if ($exists) {
            // Update the pivot to toggle is_active
            $pivot = $user->platforms()->where('platform_id', $platform->id)->first()->pivot;
            $isActive = !$pivot->is_active;
            
            $user->platforms()->updateExistingPivot($platform->id, [
                'is_active' => $isActive
            ]);
            
            $action = $isActive ? 'activate' : 'deactivate';
        } else {
            // Attach the platform with is_active = true
            $user->platforms()->attach($platform->id, [
                'is_active' => true,
                'credentials' => null
            ]);
            
            $isActive = true;
            $action = 'activate';
        }
        
        // Log the activity
        ActivityLog::log(
            $user->id,
            $action,
            'platform',
            $platform->id,
            ['platform_name' => $platform->name]
        );
        
        return response()->json([
            'platform' => $platform,
            'is_active' => $isActive,
            'message' => "Platform {$platform->name} " . ($isActive ? 'activated' : 'deactivated')
        ]);
    }

    /**
     * Update platform credentials for the authenticated user.
     */
    public function updateCredentials(Request $request, string $id)
    {
        $request->validate([
            'credentials' => 'required|array'
        ]);
        
        $platform = Platform::findOrFail($id);
        $user = Auth::user();
        
        // Check if user already has this platform
        $exists = $user->platforms()->where('platform_id', $platform->id)->exists();
        
        if ($exists) {
            $user->platforms()->updateExistingPivot($platform->id, [
                'credentials' => $request->credentials
            ]);
        } else {
            $user->platforms()->attach($platform->id, [
                'is_active' => true,
                'credentials' => $request->credentials
            ]);
        }
        
        // Log the activity
        ActivityLog::log(
            $user->id,
            'update_credentials',
            'platform',
            $platform->id,
            ['platform_name' => $platform->name]
        );
        
        return response()->json([
            'message' => "Credentials for {$platform->name} updated successfully"
        ]);
    }
}
