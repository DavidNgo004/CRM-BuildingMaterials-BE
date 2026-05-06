<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * GET /api/activity-logs
     * Query params: action, entity_type, user_id, from, to, search, per_page
     */
    public function index(Request $request)
    {
        $filters = $request->only(['action', 'entity_type', 'user_id', 'from', 'to', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $logs = ActivityLogService::paginate($filters, $perPage);

        return response()->json($logs);
    }
}
