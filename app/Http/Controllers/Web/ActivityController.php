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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

        $dateFrom = $request->filled('date_from')
            ? $request->date_from
            : now()->startOfMonth()->toDateString();

        $dateTo = $request->filled('date_to')
            ? $request->date_to
            : now()->endOfMonth()->toDateString();

        $query->whereDate('created_at', '>=', $dateFrom)
              ->whereDate('created_at', '<=', $dateTo);

        $activities = $query->latest()->get();

        return view('activities.index', compact('activities', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request)
    {
        $query = Activity::with(['assignee', 'team', 'creator', 'logs.sparepart']);

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

        $dateFrom = $request->filled('date_from')
            ? $request->date_from
            : now()->startOfMonth()->toDateString();

        $dateTo = $request->filled('date_to')
            ? $request->date_to
            : now()->endOfMonth()->toDateString();

        $query->whereDate('created_at', '>=', $dateFrom)
              ->whereDate('created_at', '<=', $dateTo);

        $activities = $query->latest()->get();

        $statusMap = [
            'completed'   => 'Selesai',
            'in_progress' => 'Diproses',
            'pending'     => 'Tertunda',
            'cancelled'   => 'Dibatalkan',
        ];
        $priorityMap = [
            'urgent' => 'Mendesak',
            'high'   => 'Tinggi',
            'medium' => 'Sedang',
            'low'    => 'Rendah',
        ];

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tiket');

        $ticketHeaders = [
            'No. Tiket', 'Judul', 'Pelapor', 'Departemen',
            'Kategori', 'Sub Kategori', 'Tipe Device', 'Barcode',
            'Status', 'Prioritas', 'Teknisi', 'Tim',
            'Dibuat', 'Selesai', 'Total Biaya Sparepart', 'Sparepart Diganti',
        ];
        $sheet->fromArray([$ticketHeaders], null, 'A1');
        $this->styleExportHeader($sheet, 'A1:P1');

        $row = 2;
        foreach ($activities as $act) {
            $sparepartLogs = $act->logs->whereNotNull('sparepart_id');
            $total = $sparepartLogs->sum(fn ($l) => (int) $l->sparepart_quantity * (float) $l->sparepart_price);
            $sparepartNames = $sparepartLogs->map(fn ($l) => $l->sparepart?->name ?? '-')->unique()->implode(', ');

            $sheet->fromArray([[
                $act->ticket_number,
                $act->title,
                $act->reporter_name,
                $act->department ?? '',
                ucfirst($act->category),
                $act->sub_category ?? '',
                $act->device_type ?? '',
                $act->barcode_number ?? '',
                $statusMap[$act->status] ?? $act->status,
                $priorityMap[$act->priority] ?? $act->priority,
                $act->assignee?->name ?? '',
                $act->team?->name ?? '',
                $act->created_at->format('d/m/Y H:i'),
                $act->completed_at ? $act->completed_at->format('d/m/Y H:i') : '',
                (float) $total,
                $sparepartNames,
            ]], null, "A{$row}");
            $row++;
        }

        $lastTicketRow = max($row - 1, 2);
        $sheet->getStyle("O2:O{$lastTicketRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:P{$lastTicketRow}");
        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Sparepart');

        $partHeaders = [
            'No', 'No. Tiket', 'Judul Tiket', 'Nama Sparepart',
            'Quantity', 'Harga Satuan', 'Subtotal', 'Tanggal Pakai',
        ];
        $sheet2->fromArray([$partHeaders], null, 'A1');
        $this->styleExportHeader($sheet2, 'A1:H1');

        $row2 = 2;
        $no = 1;
        $grandTotal = 0;
        foreach ($activities as $act) {
            foreach ($act->logs->whereNotNull('sparepart_id') as $log) {
                $subtotal = (int) $log->sparepart_quantity * (float) $log->sparepart_price;
                $grandTotal += $subtotal;

                $sheet2->fromArray([[
                    $no++,
                    $act->ticket_number,
                    $act->title,
                    $log->sparepart?->name ?? '-',
                    (int) $log->sparepart_quantity,
                    (float) $log->sparepart_price,
                    (float) $subtotal,
                    $log->created_at->format('d/m/Y H:i'),
                ]], null, "A{$row2}");
                $row2++;
            }
        }

        if ($row2 > 2) {
            $sheet2->fromArray([['', '', '', 'TOTAL', '', '', (float) $grandTotal, '']], null, "A{$row2}");
            $sheet2->getStyle("A{$row2}:H{$row2}")->getFont()->setBold(true);
        }

        $lastPartRow = max($row2 - 1, 2);
        $sheet2->getStyle("F2:G{$lastPartRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet2->freezePane('A2');
        $sheet2->setAutoFilter("A1:H" . max($row2 - ($row2 > 2 ? 1 : 0), 2));
        foreach (range('A', 'H') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'tiket_' . $dateFrom . '_sd_' . $dateTo . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function styleExportHeader($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E293B']],
        ]);
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
            'Printer' => 'Printer',
            'Scanner' => 'Scanner',
            'Monitor' => 'Monitor',
            'Proyektor' => 'Proyektor',
            'CCTV' => 'CCTV'
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
