<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary()
    {
        try {
            $today = now()->toDateString();

            $total = Activity::whereDate('created_at', $today)->count();
            $pending = Activity::whereDate('created_at', $today)
                ->where('status', 'pending')
                ->count();
            $inProgress = Activity::whereDate('created_at', $today)
                ->where('status', 'in_progress')
                ->count();
            $completed = Activity::whereDate('created_at', $today)
                ->where('status', 'completed')
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'pending' => $pending,
                    'in_progress' => $inProgress,
                    'completed' => $completed,
                ],
                'message' => 'Ringkasan dashboard berhasil diambil',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil ringkasan dashboard',
            ], 500);
        }
    }

    public function weekly()
    {
        try {
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

            $result = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->toDateString();
                $dayData = [
                    'date' => $date,
                    'pending' => 0,
                    'in_progress' => 0,
                    'completed' => 0,
                    'total' => 0,
                ];

                if (isset($activities[$date])) {
                    foreach ($activities[$date] as $activity) {
                        $dayData[$activity->status] = $activity->count;
                        $dayData['total'] += $activity->count;
                    }
                }

                $result[] = $dayData;
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Data aktivitas mingguan berhasil diambil',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data aktivitas mingguan',
            ], 500);
        }
    }

    public function teamStats()
    {
        try {
            $teams = Team::withCount(['activities as total_activities'])
                ->withCount(['activities as completed_activities' => function ($query) {
                    $query->where('status', 'completed');
                }])
                ->get()
                ->map(function ($team) {
                    $team->completion_ratio = $team->total_activities > 0
                        ? round(($team->completed_activities / $team->total_activities) * 100, 2)
                        : 0;
                    $team->workload = $team->total_activities - $team->completed_activities;
                    return $team;
                });

            return response()->json([
                'success' => true,
                'data' => $teams,
                'message' => 'Statistik tim berhasil diambil',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil statistik tim',
            ], 500);
        }
    }
}
