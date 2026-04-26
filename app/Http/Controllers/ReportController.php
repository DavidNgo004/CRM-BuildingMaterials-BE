<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Report::with('user')->latest();

        // Warehouse staff chỉ thấy báo cáo của mình
        if ($user->role === 'warehouse_staff') {
            $query->where('user_id', $user->id);
        }

        $reports = $query->paginate($request->get('per_page', 20));
        return response()->json($reports);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'type'    => 'required|in:inventory,incident,general,request',
        ]);

        $report = Report::create([
            ...$validated,
            'user_id' => Auth::id(),
            'status'  => 'pending',
        ]);

        return response()->json($report->load('user'), 201);
    }

    public function markSeen(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $report->update([
            'status'  => 'seen',
            'seen_at' => now(),
        ]);
        return response()->json($report->load('user'));
    }

    public function reply(Request $request, $id)
    {
        $validated = $request->validate([
            'admin_reply' => 'required|string',
        ]);

        $report = Report::findOrFail($id);
        $report->update([
            ...$validated,
            'status'  => 'resolved',
            'seen_at' => $report->seen_at ?? now(),
        ]);
        return response()->json($report->load('user'));
    }
}
