<?php

namespace App\Http\Controllers\ImportExport;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Size;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;

class ProductImportExportController extends Controller
{
    /* ─────────────────────────────────────────────
     |  EXPORT  (CSV + Excel)
     ────────────────────────────────────────────── */

    public function export(Request $request): StreamedResponse
    {
        $format = $request->get('format', 'csv');

        $query = Product::with('category');

        if (!$request->filled('status')) {
            $query->whereIn('status', ['active', 'inactive']);
        } else {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('detail', 'LIKE', "%{$search}%")
                    ->orWhereHas('category', fn($q) => $q->where('name', 'LIKE', "%{$search}%"));
            });
        }

        $products = $query->orderBy('created_at', 'asc')->get();

        // Build lookup maps keyed by int id
        $sizes  = Size::all()->keyBy('id');
        $colors = Color::all()->keyBy('id');

        $headers = ['ID', 'Name', 'Detail', 'Category', 'Price', 'Sizes', 'Colors', 'Image', 'Status', 'Created At'];

        $rows = $products->map(function ($p) use ($sizes, $colors) {

            // int cast fix — size names
            $sizeNames = collect(explode(',', $p->size_id ?? ''))
                ->map('trim')->filter()
                ->map(fn($id) => isset($sizes[(int)$id]) ? $sizes[(int)$id]->name : '')
                ->filter()->implode(', ');

            // int cast fix — color names
            $colorNames = collect(explode(',', $p->color_id ?? ''))
                ->map('trim')->filter()
                ->map(fn($id) => isset($colors[(int)$id]) ? $colors[(int)$id]->name : '')
                ->filter()->implode(', ');

            return [
                $p->id,
                $p->name,
                $p->detail,
                $p->category->name ?? '-',
                number_format($p->price, 2, '.', ''),
                $sizeNames,
                $colorNames,
                $p->image ?? '',   // comma-separated filenames e.g. "abc.jpg,xyz.jpg"
                $p->status,
                $p->created_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();

        $filename = 'products_export_' . now()->format('Ymd_His');

        if ($format === 'excel') {
            return $this->streamExcel($filename . '.xlsx', $headers, $rows);
        }

        return $this->streamCsv($filename . '.csv', $headers, $rows);
    }

    /* ─────────────────────────────────────────────
     |  IMPORT  (CSV + Excel)
     ────────────────────────────────────────────── */

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        $file      = $request->file('import_file');
        $extension = strtolower($file->getClientOriginalExtension());

        try {
            $rows = $extension === 'csv' || $extension === 'txt'
                ? $this->parseCsv($file->getRealPath())
                : $this->parseExcel($file->getRealPath());
        } catch (\Exception $e) {
            return back()->with('error', 'Could not parse file: ' . $e->getMessage());
        }

        if (empty($rows)) {
            return back()->with('error', 'The file is empty or has no valid rows.');
        }

        $required = ['name', 'detail', 'category', 'price'];
        $header   = array_map('strtolower', array_map('trim', array_shift($rows)));
        $missing  = array_diff($required, $header);

        if (!empty($missing)) {
            return back()->with('error', 'Missing required columns: ' . implode(', ', $missing));
        }

        $colIndex = array_flip($header);

        // Build lookup maps (name → id, case-insensitive)
        $categories = Category::pluck('id', 'name')->mapWithKeys(fn($id, $n) => [strtolower($n) => $id]);
        $sizes      = Size::pluck('id', 'name')->mapWithKeys(fn($id, $n) => [strtolower($n) => $id]);
        $colors     = Color::pluck('id', 'name')->mapWithKeys(fn($id, $n) => [strtolower($n) => $id]);

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $i => $row) {
                $lineNo = $i + 2;

                if (count($row) < count($header)) {
                    $row = array_pad($row, count($header), '');
                }

                $name    = trim($row[$colIndex['name']]     ?? '');
                $detail  = trim($row[$colIndex['detail']]   ?? '');
                $catName = trim($row[$colIndex['category']] ?? '');
                $price   = $row[$colIndex['price']]         ?? 0;

                if (!$name) {
                    $errors[] = "Row {$lineNo}: name is required.";
                    $skipped++;
                    continue;
                }
                if (!is_numeric($price)) {
                    $errors[] = "Row {$lineNo}: invalid price '{$price}'.";
                    $skipped++;
                    continue;
                }

                $categoryId = $categories[strtolower($catName)] ?? null;
                if (!$categoryId) {
                    $errors[] = "Row {$lineNo}: category '{$catName}' not found.";
                    $skipped++;
                    continue;
                }


                // ── DUPLICATE CHECK ──
                // Same name + category + price already exists → skip
                $alreadyExists = Product::where('name', $name)
                    ->where('category_id', $categoryId)
                    ->where('price', (float) $price)
                    ->whereIn('status', ['active', 'inactive'])
                    ->exists();

                if ($alreadyExists) {
                    $skipped++;
                    $errors[] = "Row {$lineNo}: duplicate skipped — '{$name}' already exists.";
                    continue;
                }

                // Optional columns
                $rawSizes  = isset($colIndex['sizes'])  ? trim($row[$colIndex['sizes']]  ?? '') : '';
                $rawColors = isset($colIndex['colors']) ? trim($row[$colIndex['colors']] ?? '') : '';
                $status    = isset($colIndex['status']) ? trim($row[$colIndex['status']] ?? 'active') : 'active';
                if (!in_array($status, ['active', 'inactive'])) $status = 'active';

                // Size IDs from names
                $sizeIds = collect(explode(',', $rawSizes))
                    ->map(fn($s) => $sizes[strtolower(trim($s))] ?? null)
                    ->filter()->implode(',');

                // Color IDs from names
                $colorIds = collect(explode(',', $rawColors))
                    ->map(fn($c) => $colors[strtolower(trim($c))] ?? null)
                    ->filter()->implode(',');

                // ── IMAGE ──
                // 'image' column = comma-separated filenames (e.g. "abc.jpg,xyz.jpg")
                // Files must already exist in public/images/products/
                $rawImage    = isset($colIndex['image']) ? trim($row[$colIndex['image']] ?? '') : '';
                $validImages = collect(explode(',', $rawImage))
                    ->map('trim')
                    ->filter()
                    ->filter(fn($img) => file_exists(public_path('images/products/' . $img)))
                    ->implode(',');

                Product::create([
                    'name'        => $name,
                    'detail'      => $detail,
                    'category_id' => $categoryId,
                    'price'       => (float) $price,
                    'size_id'     => $sizeIds,
                    'color_id'    => $colorIds,
                    'image'       => $validImages,
                    'status'      => $status,
                ]);

                $imported++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        $message = "Import complete. {$imported} product(s) imported.";
        if ($skipped) $message .= " {$skipped} row(s) skipped.";
        if (!empty($errors)) $message .= ' Issues: ' . implode(' | ', array_slice($errors, 0, 5));

        return redirect()->route('products.index')->with('success', $message);
    }

    /* ─────────────────────────────────────────────
     |  TEMPLATE DOWNLOAD
     ────────────────────────────────────────────── */

    public function template(): StreamedResponse
    {
        $headers = ['name', 'detail', 'category', 'price', 'sizes', 'colors', 'image', 'status'];
        $example = [
            'Sample Product',
            'This is a product description',
            'Electronics',
            '99.99',
            'S, M, L',
            'Red, Blue',
            'product1.jpg',   // must exist in public/images/products/
            'active',
        ];

        return response()->streamDownload(function () use ($headers, $example) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);
            fputcsv($handle, $example);
            fclose($handle);
        }, 'products_import_template.csv', [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="products_import_template.csv"',
        ]);
    }

    /* ─────────────────────────────────────────────
     |  PRIVATE HELPERS
     ────────────────────────────────────────────── */

    private function streamCsv(string $filename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
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

    private function streamExcel(string $filename, array $headers, array $rows): StreamedResponse
    {
        if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            return $this->streamWithSpreadsheet($filename, $headers, $rows);
        }

        // Fallback: SpreadsheetML (opens in Excel without extra library)
        return response()->streamDownload(function () use ($headers, $rows) {
            echo $this->buildSpreadsheetML($headers, $rows);
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function streamWithSpreadsheet(string $filename, array $headers, array $rows): StreamedResponse
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $sheet->fromArray($headers, null, 'A1');

        $lastCol     = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF4F81BD']],
            'alignment' => ['horizontal' => 'center'],
        ];
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray($headerStyle);

        foreach ($rows as $ri => $row) {
            $sheet->fromArray($row, null, 'A' . ($ri + 2));
        }

        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function buildSpreadsheetML(array $headers, array $rows): string
    {
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
                   xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . PHP_EOL;
        $xml .= '<Worksheet ss:Name="Products"><Table>' . PHP_EOL;

        $xml .= '<Row>';
        foreach ($headers as $h) {
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($h) . '</Data></Cell>';
        }
        $xml .= '</Row>' . PHP_EOL;

        foreach ($rows as $row) {
            $xml .= '<Row>';
            foreach ($row as $cell) {
                $type = is_numeric($cell) ? 'Number' : 'String';
                $xml .= '<Cell><Data ss:Type="' . $type . '">' . htmlspecialchars((string)$cell) . '</Data></Cell>';
            }
            $xml .= '</Row>' . PHP_EOL;
        }

        $xml .= '</Table></Worksheet></Workbook>';
        return $xml;
    }

    private function parseCsv(string $path): array
    {
        $rows   = [];
        $handle = fopen($path, 'r');
        $bom    = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }
        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }
        fclose($handle);
        return $rows;
    }

    private function parseExcel(string $path): array
    {
        if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            throw new \RuntimeException(
                'PhpSpreadsheet is not installed. Run: composer require phpoffice/phpspreadsheet'
            );
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = [];

        foreach ($sheet->getRowIterator() as $row) {
            $cells = [];
            foreach ($row->getCellIterator() as $cell) {
                $cells[] = (string) $cell->getValue();
            }
            $rows[] = $cells;
        }

        return $rows;
    }
}
