<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::where('user_id', Auth::id());
        
        // Filter by action if provided
        if ($request->has('action')) {
            $query->where('action', $request->action);
        }
        
        // Filter by entity type if provided
        if ($request->has('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }
        
        // Filter by date range if provided
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        // Get logs with pagination
        $logs = $query->latest()->paginate(20);
        
        return response()->json($logs);
    }
    
    /**
     * Get activity log summary with counts by entity type and action.
     */
    public function summary()
    {
        $userId = Auth::id();
        
        // Get counts by entity type
        $entityTypeCounts = ActivityLog::where('user_id', $userId)
            ->select('entity_type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('entity_type')
            ->get();
        
        // Get counts by action
        $actionCounts = ActivityLog::where('user_id', $userId)
            ->select('action')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('action')
            ->get();
        
        // Get recent activity
        $recentActivity = ActivityLog::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();
        
        return response()->json([
            'entity_type_counts' => $entityTypeCounts,
            'action_counts' => $actionCounts,
            'recent_activity' => $recentActivity,
        ]);
    }
} 