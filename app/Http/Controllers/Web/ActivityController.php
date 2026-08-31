<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\Sparepart;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['assignee', 'team', 'creator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('reporter_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $activities = $query->latest()->get();

        return view('activities.index', compact('activities'));
    }

    public function create()
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        $teams = Team::orderBy('name')->get();
        $currentUser = session('web_user');

        return view('activities.create', compact('users', 'teams', 'currentUser'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|in:hardware,software,network,other',
            'sub_category' => 'nullable|string|max:100',
            'device_type' => 'nullable|string|max:100',
            'barcode_number' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'team_id' => 'nullable|exists:teams,id',
            'reporter_name' => 'required|string|max:255',
            'reporter_phone' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $validated['created_by'] = session('web_user.id');
        $validated['status'] = 'pending';

        $activity = Activity::create($validated);

        ActivityLog::create([
            'activity_id' => $activity->id,
            'user_id' => session('web_user.id'),
            'status' => 'pending',
            'note' => 'Aktivitas dibuat',
        ]);

        return redirect()->route('activities.index')->with('success', 'Tiket berhasil dibuat.');
    }

    public function show(Activity $activity)
    {
        $activity->load(['assignee', 'team', 'creator', 'logs.user', 'logs.sparepart']);
        $users = User::where('is_active', true)->orderBy('name')->get();
        $teams = Team::orderBy('name')->get();
        $spareparts = Sparepart::orderBy('name')->get();

        return view('activities.show', compact('activity', 'users', 'teams', 'spareparts'));
    }

    public function edit(Activity $activity)
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        $teams = Team::orderBy('name')->get();

        return view('activities.edit', compact('activity', 'users', 'teams'));
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|in:hardware,software,network,other',
            'sub_category' => 'nullable|string|max:100',
            'device_type' => 'nullable|string|max:100',
            'barcode_number' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'team_id' => 'nullable|exists:teams,id',
            'reporter_name' => 'required|string|max:255',
            'reporter_phone' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $activity->update($validated);

        return redirect()->route('activities.show', $activity)->with('success', 'Tiket berhasil diupdate.');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return redirect()->route('activities.index')->with('success', 'Tiket berhasil dihapus.');
    }

    public function searchAssets(Request $request)
    {
        $subcategory = $request->input('subcategory', '');

        $categoryMap = [
            'Laptop' => 'Laptop',
            'Desktop' => 'PC Desktop',
        ];

        if (!isset($categoryMap[$subcategory])) {
            return response()->json([]);
        }

        $assets = Asset::where('category', $categoryMap[$subcategory])
            ->select('asset_number', 'asset_name')
            ->orderBy('asset_name')
            ->get();

        return response()->json($assets);
    }

    public function updateStatus(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,in_progress,completed,cancelled',
            'repair_description' => 'nullable|string',
        ]);

        $oldStatus = $activity->status;
        $newStatus = $validated['status'];

        $updateData = ['status' => $newStatus];
        if ($newStatus === 'completed') {
            $updateData['completed_at'] = now();
        }

        $activity->update($updateData);

        $repairData = null;
        $logNote = "Status diubah dari '{$oldStatus}' ke '{$newStatus}'";

        if ($newStatus === 'completed' && !empty($validated['repair_description'])) {
            $repairData = [
                'description' => $validated['repair_description'],
            ];
            $logNote = $validated['repair_description'];
        }

        $log = ActivityLog::create([
            'activity_id' => $activity->id,
            'user_id' => session('web_user.id'),
            'status' => $newStatus,
            'note' => $logNote,
            'repair_data' => $repairData,
        ]);

        // Handle spareparts - filter out empty entries
        $rawSpareparts = $request->input('spareparts', []);
        $spareparts = array_filter($rawSpareparts, fn($item) => !empty($item['id']));

        $first = true;
        foreach ($spareparts as $item) {
            $sparepart = Sparepart::find($item['id']);
            $qty = $item['quantity'] ?? 1;

            if ($sparepart && $sparepart->stock >= $qty) {
                $sparepart->decrement('stock', $qty);

                if ($first) {
                    $log->update([
                        'sparepart_id' => $sparepart->id,
                        'sparepart_quantity' => $qty,
                        'sparepart_price' => $sparepart->price,
                    ]);
                    $first = false;
                } else {
                    ActivityLog::create([
                        'activity_id' => $activity->id,
                        'user_id' => session('web_user.id'),
                        'status' => $newStatus,
                        'note' => 'Sparepart: ' . $sparepart->name,
                        'sparepart_id' => $sparepart->id,
                        'sparepart_quantity' => $qty,
                        'sparepart_price' => $sparepart->price,
                    ]);
                }
            }
        }

        return back()->with('success', 'Status berhasil diubah.');
    }
}
