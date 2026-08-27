<?php

namespace App\Support;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExport
{
    /**
     * Bikin file Excel (.xlsx) pakai PhpSpreadsheet.
     * $rows: iterable, tiap item array nilai sel selaras urutan $headers.
     */
    public static function download(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row — bold
        foreach ($headers as $col => $head) {
            $cell = $sheet->setCellValue([$col + 1, 1], $head);
        }
        $sheet->getStyle('1:1')->getFont()->setBold(true);

        // Data rows
        $rowNum = 2;
        foreach ($rows as $row) {
            foreach (array_values(is_array($row) ? $row : $row->toArray()) as $col => $value) {
                $sheet->setCellValue([$col + 1, $rowNum], $value);
            }
            $rowNum++;
        }

        // Auto-size columns
        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
