<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\CostOverhead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->get('type', 'tickets');
        $dateFrom = $request->filled('date_from') ? $request->date_from : now()->startOfMonth()->toDateString();
        $dateTo = $request->filled('date_to') ? $request->date_to : now()->endOfMonth()->toDateString();

        if ($reportType === 'tickets') {
            $data = $this->getTicketReportData($dateFrom, $dateTo);
            $aiAnalysis = $this->getAiAnalysis('tickets', $data, $dateFrom, $dateTo);
            $chartData = $this->getTicketChartData($data);
        } else {
            $data = $this->getCostOverheadReportData($dateFrom, $dateTo);
            $aiAnalysis = $this->getAiAnalysis('cost_overhead', $data, $dateFrom, $dateTo);
            $chartData = $this->getCostOverheadChartData($data);
        }

        return view('reports.index', compact('reportType', 'dateFrom', 'dateTo', 'data', 'aiAnalysis', 'chartData'));
    }

    private function getTicketReportData(string $dateFrom, string $dateTo): array
    {
        $query = Activity::with(['assignee', 'team', 'logs.sparepart']);

        $query->whereDate('created_at', '>=', $dateFrom)
              ->whereDate('created_at', '<=', $dateTo);

        $activities = $query->get();

        $totalTickets = $activities->count();
        $completed = $activities->where('status', 'completed')->count();
        $inProgress = $activities->where('status', 'in_progress')->count();
        $pending = $activities->where('status', 'pending')->count();
        $cancelled = $activities->where('status', 'cancelled')->count();

        $byCategory = $activities->groupBy('category')->map->count()->toArray();
        $byPriority = $activities->groupBy('priority')->map->count()->toArray();
        $byStatus = $activities->groupBy('status')->map->count()->toArray();
        $byTeam = $activities->groupBy('team.name')->map->count()->toArray();
        $byAssignee = $activities->groupBy('assignee.name')->map->count()->toArray();

        $avgResolutionDays = $activities->where('status', 'completed')
            ->filter(fn($a) => $a->completed_at)
            ->map(fn($a) => $a->created_at->diffInDays($a->completed_at))
            ->avg() ?? 0;

        $totalSparepartCost = $activities->flatMap->logs
            ->whereNotNull('sparepart_id')
            ->sum(fn($l) => (int) $l->sparepart_quantity * (float) $l->sparepart_price);

        $topIssues = $activities->groupBy('title')
            ->map->count()
            ->sortDesc()
            ->take(5)
            ->toArray();

        return [
            'summary' => [
                'total_tickets' => $totalTickets,
                'completed' => $completed,
                'in_progress' => $inProgress,
                'pending' => $pending,
                'cancelled' => $cancelled,
                'avg_resolution_days' => round($avgResolutionDays, 1),
                'total_sparepart_cost' => $totalSparepartCost,
            ],
            'by_category' => $byCategory,
            'by_priority' => $byPriority,
            'by_status' => $byStatus,
            'by_team' => $byTeam,
            'by_assignee' => $byAssignee,
            'top_issues' => $topIssues,
        ];
    }

    private function getCostOverheadReportData(string $dateFrom, string $dateTo): array
    {
        $query = CostOverhead::query();

        $query->whereDate('posting_date', '>=', $dateFrom)
              ->whereDate('posting_date', '<=', $dateTo);

        $records = $query->get();

        $totalAmount = $records->sum('amount');
        $totalRecords = $records->count();
        $avgAmount = $totalRecords > 0 ? $totalAmount / $totalRecords : 0;

        $byGlAccount = $records->groupBy('gl_account')->map(function ($items) {
            return [
                'gl_name' => $items->first()->gl_name,
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ];
        })->sortByDesc('total')->toArray();

        $byProfitCenter = $records->groupBy('profit_center')->map(function ($items) {
            return [
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ];
        })->sortByDesc('total')->toArray();

        $byCompanyCode = $records->groupBy('company_code')->map(function ($items) {
            return [
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ];
        })->sortByDesc('total')->toArray();

        $byDepartemen = $records->groupBy('departemen')->map(function ($items) {
            return [
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ];
        })->sortByDesc('total')->toArray();

        $monthlyTrend = $records->groupBy(function ($item) {
            return $item->posting_date?->format('Y-m');
        })->map(function ($items) {
            return [
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ];
        })->sortKeys()->toArray();

        return [
            'summary' => [
                'total_amount' => $totalAmount,
                'total_records' => $totalRecords,
                'avg_amount' => round($avgAmount, 0),
            ],
            'by_gl_account' => $byGlAccount,
            'by_profit_center' => $byProfitCenter,
            'by_company_code' => $byCompanyCode,
            'by_departemen' => $byDepartemen,
            'monthly_trend' => $monthlyTrend,
        ];
    }

    private function getTicketChartData(array $data): array
    {
        $statusColors = [
            'completed' => '#22c55e',
            'in_progress' => '#3b82f6',
            'pending' => '#f59e0b',
            'cancelled' => '#ef4444',
        ];

        $priorityColors = [
            'urgent' => '#ef4444',
            'high' => '#f97316',
            'medium' => '#eab308',
            'low' => '#22c55e',
        ];

        return [
            'status' => [
                'labels' => array_keys($data['by_status']),
                'data' => array_values($data['by_status']),
                'colors' => array_map(fn($k) => $statusColors[$k] ?? '#64748b', array_keys($data['by_status'])),
            ],
            'priority' => [
                'labels' => array_keys($data['by_priority']),
                'data' => array_values($data['by_priority']),
                'colors' => array_map(fn($k) => $priorityColors[$k] ?? '#64748b', array_keys($data['by_priority'])),
            ],
            'category' => [
                'labels' => array_keys($data['by_category']),
                'data' => array_values($data['by_category']),
            ],
            'team' => [
                'labels' => array_keys($data['by_team']),
                'data' => array_values($data['by_team']),
            ],
        ];
    }

    private function getCostOverheadChartData(array $data): array
    {
        return [
            'monthly' => [
                'labels' => array_keys($data['monthly_trend']),
                'data' => array_map(fn($v) => $v['total'], $data['monthly_trend']),
            ],
            'gl_account' => [
                'labels' => array_map(fn($v) => $v['gl_name'] ?? 'N/A', $data['by_gl_account']),
                'data' => array_map(fn($v) => $v['total'], $data['by_gl_account']),
            ],
            'profit_center' => [
                'labels' => array_keys($data['by_profit_center']),
                'data' => array_map(fn($v) => $v['total'], $data['by_profit_center']),
            ],
            'departemen' => [
                'labels' => array_keys($data['by_departemen']),
                'data' => array_map(fn($v) => $v['total'], $data['by_departemen']),
            ],
        ];
    }

    private function getAiAnalysis(string $type, array $data, string $dateFrom, string $dateTo): string
    {
        $prompt = $this->buildPrompt($type, $data, $dateFrom, $dateTo);

        try {
            $apiUrl = rtrim(env('AI_API_URL', 'https://9router.fithlail.my.id/v1'), '/');
            $apiKey = env('AI_API_KEY', 'sk-d3a40b875db690e2-2zrwlr-1957b2d7');
            $model = env('AI_MODEL', 'gpt-4o-mini');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->withoutVerifying() // Disable SSL verify for development
              ->timeout(30)->post($apiUrl . '/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'Anda adalah analis data IT Helpdesk dan Keuangan. Berikan analisis singkat, actionable, dan insight dalam bahasa Indonesia. Fokus pada tren, anomali, dan rekomendasi. Maksimal 3 paragraf.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
                'max_tokens' => 800,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                if ($content) {
                    return $content;
                }
            } else {
                \Log::warning('AI Analysis API error: ' . $response->status() . ' - ' . $response->body());
            }
        } catch (\Exception $e) {
            \Log::error('AI Analysis failed: ' . $e->getMessage());
        }

        return 'Gagal mendapatkan analisis AI. Silakan coba lagi nanti.';
    }

    private function buildPrompt(string $type, array $data, string $dateFrom, string $dateTo): string
    {
        if ($type === 'tickets') {
            $s = $data['summary'];
            return "Analisis laporan tiket Helpdesk periode {$dateFrom} s/d {$dateTo}:
- Total tiket: {$s['total_tickets']} (Selesai: {$s['completed']}, Diproses: {$s['in_progress']}, Tertunda: {$s['pending']}, Dibatalkan: {$s['cancelled']})
- Rata-rata resolusi: {$s['avg_resolution_days']} hari
- Total biaya sparepart: Rp " . number_format($s['total_sparepart_cost'], 0, ',', '.') . "
- Kategori: " . json_encode($data['by_category']) . "
- Prioritas: " . json_encode($data['by_priority']) . "
- Tim: " . json_encode($data['by_team']) . "
- Top 5 issues: " . json_encode($data['top_issues']) . "

Berikan: 1) Insight utama, 2) Area perhatian/perbaikan, 3) Rekomendasi aksi.";
        }

        $s = $data['summary'];
        return "Analisis laporan Cost Overhead periode {$dateFrom} s/d {$dateTo}:
- Total records: {$s['total_records']}
- Total amount: Rp " . number_format($s['total_amount'], 0, ',', '.') . "
- Rata-rata per record: Rp " . number_format($s['avg_amount'], 0, ',', '.') . "
- Top 5 GL Account: " . json_encode(array_slice($data['by_gl_account'], 0, 5, true)) . "
- Profit Center: " . json_encode(array_slice($data['by_profit_center'], 0, 5, true)) . "
- Departemen: " . json_encode(array_slice($data['by_departemen'], 0, 5, true)) . "
- Trend bulanan: " . json_encode($data['monthly_trend']) . "

Berikan: 1) Insight utama pengeluaran, 2) Anomali/spike yang perlu diperhatikan, 3) Rekomendasi pengendalian biaya.";
    }
}