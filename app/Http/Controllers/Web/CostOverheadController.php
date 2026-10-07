<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CostOverhead;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CostOverheadController extends Controller
{
    public function index(Request $request)
    {
        $query = CostOverhead::query();

        if ($request->filled('date_from')) {
            $query->whereDate('posting_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('posting_date', '<=', $request->date_to);
        }

        $costOverheads = $query->orderByDesc('posting_date')->paginate(10)->withQueryString();

        return view('cost-overheads.index', compact('costOverheads'));
    }

    public function create()
    {
        return view('cost-overheads.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gl_account'      => 'required|integer',
            'gl_name'         => 'nullable|string',
            'posting_date'    => 'nullable|date',
            'amount'          => 'nullable|integer',
            'text'            => 'nullable|string',
            'document_header' => 'nullable|string',
            'profit_center'   => 'nullable|string|max:255',
            'company_code'    => 'nullable|string|max:10',
            'departemen'      => 'nullable|string|max:255',
            'user'            => 'nullable|string|max:255',
            'activity'        => 'nullable|string|max:255',
        ]);

        CostOverhead::create($validated);

        return redirect()->route('cost-overheads.index')->with('success', 'Data Cost Overhead berhasil ditambahkan.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();
            $inserted = 0;

            for ($i = 2; $i <= $highestRow; $i++) {
                $glAccount = $sheet->getCell('A' . $i)->getValue();
                if (empty($glAccount)) continue;

                $postingDate = null;
                $dateCell = $sheet->getCell('C' . $i);
                $dateRaw = $dateCell->getValue();

                if (!empty($dateRaw)) {
                    try {
                        if (is_numeric($dateRaw) && \PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($dateCell)) {
                            $postingDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $dateRaw)
                                ->format('Y-m-d');
                        } elseif (is_numeric($dateRaw)) {
                            $postingDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $dateRaw)
                                ->format('Y-m-d');
                        } else {
                            $dateStr = trim((string) $dateRaw);
                            $d = \DateTime::createFromFormat('d/m/Y', $dateStr)
                                ?: \DateTime::createFromFormat('d-m-Y', $dateStr)
                                ?: \DateTime::createFromFormat('Y-m-d', $dateStr);
                            $postingDate = $d ? $d->format('Y-m-d') : null;
                        }
                    } catch (\Exception $e) {
                        $postingDate = null;
                    }
                }

                $rawAmount = $sheet->getCell('D' . $i)->getValue();
                $amount = null;
                if (!is_null($rawAmount) && $rawAmount !== '') {
                    $cleaned = preg_replace('/[^\d]/', '', (string) $rawAmount);
                    $amount = $cleaned !== '' ? (int) $cleaned : null;
                }

                CostOverhead::create([
                    'gl_account'      => (int) $glAccount,
                    'gl_name'         => $sheet->getCell('B' . $i)->getValue() ?? '',
                    'posting_date'    => $postingDate,
                    'amount'          => $amount,
                    'text'            => $sheet->getCell('E' . $i)->getValue() ?: null,
                    'document_header' => $sheet->getCell('F' . $i)->getValue() ?: null,
                    'profit_center'   => $sheet->getCell('G' . $i)->getValue() ?: null,
                    'company_code'    => $sheet->getCell('H' . $i)->getValue() ?: null,
                    'departemen'      => $sheet->getCell('I' . $i)->getValue() ?: null,
                    'user'            => $sheet->getCell('J' . $i)->getValue() ?: null,
                    'activity'        => $sheet->getCell('K' . $i)->getValue() ?: null,
                ]);
                $inserted++;
            }

            return redirect()->route('cost-overheads.index')->with('success', "$inserted data berhasil diimport.");
        } catch (\Exception $e) {
            return redirect()->route('cost-overheads.index')->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $query = CostOverhead::query();

        if ($request->filled('date_from')) {
            $query->whereDate('posting_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('posting_date', '<=', $request->date_to);
        }

        $data = $query->orderByDesc('posting_date')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['GL Account', 'GL Name', 'Posting Date', 'Amount', 'Text', 'Document Header', 'Profit Center', 'Company Code', 'Departemen', 'User', 'Activity'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];

        foreach ($cols as $idx => $col) {
            $sheet->setCellValue($col . '1', $headers[$idx]);
        }

        foreach ($data as $i => $row) {
            $r = $i + 2;
            $sheet->setCellValue('A' . $r, $row->gl_account);
            $sheet->setCellValue('B' . $r, $row->gl_name);
            $sheet->setCellValue('C' . $r, $row->posting_date?->format('Y-m-d'));
            $sheet->setCellValue('D' . $r, $row->amount);
            $sheet->setCellValue('E' . $r, $row->text);
            $sheet->setCellValue('F' . $r, $row->document_header);
            $sheet->setCellValue('G' . $r, $row->profit_center);
            $sheet->setCellValue('H' . $r, $row->company_code);
            $sheet->setCellValue('I' . $r, $row->departemen);
            $sheet->setCellValue('J' . $r, $row->user);
            $sheet->setCellValue('K' . $r, $row->activity);
        }

        $filename = 'cost_overhead_' . now()->format('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
