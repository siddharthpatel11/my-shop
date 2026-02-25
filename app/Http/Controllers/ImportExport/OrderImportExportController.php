<?php

namespace App\Http\Controllers\ImportExport;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderImportExportController extends Controller
{
    /**
     * Export orders to Excel (.xlsx) or CSV
     * Applies same filters as AdminOrderController@index
     *
     * Query params:
     *   format         = excel | csv   (default: excel)
     *   order_status   = pending | processing | shipped | delivered | cancelled
     *   payment_status = pending | paid | failed
     *   date_from      = Y-m-d
     *   date_to        = Y-m-d
     *   search         = string
     */
    public function export(Request $request): StreamedResponse
    {
        $query = Order::with(['customer', 'items.product', 'address', 'tax'])
            ->where('status', 'active');

        // -- Same filters as index --

        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->get();

        $headers = [
            'Order Number',
            'Customer Name',
            'Customer Email',
            'Order Date',
            'Order Status',
            'Payment Status',
            'Payment Method',
            'Subtotal (₹)',
            'Tax (₹)',
            'Discount (₹)',
            'Total (₹)',
            'Items Count',
            'Delivery Status',
        ];

        $rows = $orders->map(fn($order) => [
            $order->order_number,
            $order->customer->name,
            $order->customer->email,
            $order->created_at->format('Y-m-d H:i:s'),
            ucfirst($order->order_status),
            ucfirst($order->payment_status),
            ucfirst($order->payment_method ?? '-'),
            number_format($order->subtotal,   2, '.', ''),
            number_format($order->tax_amount, 2, '.', ''),
            number_format($order->discount,   2, '.', ''),
            number_format($order->total,      2, '.', ''),
            $order->items->count(),
            $order->getDeliveryStatusMessage(),
        ])->toArray();

        // ----- Format selection -----
        $format = strtolower($request->get('format', 'excel'));

        if ($format === 'csv') {
            return $this->streamCsv($headers, $rows);
        }

        // Excel path
        $filename = 'orders_export_' . now()->format('Ymd_His') . '.xlsx';

        if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            return $this->streamWithSpreadsheet($filename, $headers, $rows);
        }

        return $this->streamSpreadsheetML($filename, $headers, $rows);
    }

    /* ─────────────────────────────────────────────
     |  PRIVATE HELPERS
     ────────────────────────────────────────────── */

    /**
     * Stream a plain CSV download (no extra dependencies required).
     */
    private function streamCsv(array $headers, array $rows): StreamedResponse
    {
        $filename = 'orders_export_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens the file with correct encoding
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Stream Excel using PhpSpreadsheet (when available).
     */
    private function streamWithSpreadsheet(string $filename, array $headers, array $rows): StreamedResponse
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Orders');

        // Header row
        $sheet->fromArray($headers, null, 'A1');

        $lastCol     = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF4F81BD']],
            'alignment' => ['horizontal' => 'center'],
        ];
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray($headerStyle);

        // Data rows
        foreach ($rows as $ri => $row) {
            $sheet->fromArray($row, null, 'A' . ($ri + 2));
        }

        // Auto-size columns
        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        // Freeze header row
        $sheet->freezePane('A2');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Fallback: SpreadsheetML — no extra dependency needed, opens in Excel.
     */
    private function streamSpreadsheetML(string $filename, array $headers, array $rows): StreamedResponse
    {
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
                   xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . PHP_EOL;
        $xml .= '<Worksheet ss:Name="Orders"><Table>' . PHP_EOL;

        // Header
        $xml .= '<Row>';
        foreach ($headers as $h) {
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($h) . '</Data></Cell>';
        }
        $xml .= '</Row>' . PHP_EOL;

        // Data
        foreach ($rows as $row) {
            $xml .= '<Row>';
            foreach ($row as $cell) {
                $type = is_numeric(str_replace([',', '.'], '', (string) $cell)) ? 'Number' : 'String';
                $xml .= '<Cell><Data ss:Type="' . $type . '">' . htmlspecialchars((string) $cell) . '</Data></Cell>';
            }
            $xml .= '</Row>' . PHP_EOL;
        }

        $xml .= '</Table></Worksheet></Workbook>';

        return response()->streamDownload(function () use ($xml) {
            echo $xml;
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
