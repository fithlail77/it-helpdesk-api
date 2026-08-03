<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $summary = [
            'total' => Activity::whereDate('created_at', $today)->count(),
            'pending' => Activity::whereDate('created_at', $today)->where('status', 'pending')->count(),
            'in_progress' => Activity::whereDate('created_at', $today)->where('status', 'in_progress')->count(),
            'completed' => Activity::whereDate('created_at', $today)->where('status', 'completed')->count(),
            'all_total' => Activity::count(),
            'all_pending' => Activity::where('status', 'pending')->count(),
            'all_in_progress' => Activity::where('status', 'in_progress')->count(),
            'all_completed' => Activity::where('status', 'completed')->count(),
        ];

        $startDate = now()->subDays(6)->toDateString();
        $endDate = now()->toDateString();

        $activities = Activity::select(
            DB::raw('DATE(created_at) as date'),
            'status',
            DB::raw('count(*) as count')
        )
        ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
        ->groupBy(DB::raw('DATE(created_at)'), 'status')
        ->get()
        ->groupBy('date');

        $weekly = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $dayData = ['date' => $date, 'pending' => 0, 'in_progress' => 0, 'completed' => 0, 'total' => 0];
            if (isset($activities[$date])) {
                foreach ($activities[$date] as $a) {
                    $dayData[$a->status] = $a->count;
                    $dayData['total'] += $a->count;
                }
            }
            $weekly[] = $dayData;
        }

        $teamStats = Team::withCount(['activities as total_activities'])
            ->withCount(['activities as completed_activities' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->get()
            ->map(function ($team) {
                $team->completion_ratio = $team->total_activities > 0
                    ? round(($team->completed_activities / $team->total_activities) * 100, 1)
                    : 0;
                return $team;
            });

        $recentActivities = Activity::with(['creator', 'assignee', 'team'])
            ->latest()
            ->take(10)
            ->get();

        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();

        return view('dashboard', compact('summary', 'weekly', 'teamStats', 'recentActivities', 'totalUsers', 'activeUsers'));
    }
}
