<?php

namespace App\Support;

use Illuminate\Http\Response;

class ExcelExport
{
    /**
     * Bikin file Excel (.xls, format SpreadsheetML) tanpa dependency tambahan.
     * $rows: iterable, tiap item array nilai sel selaras urutan $headers.
     */
    public static function download(string $filename, array $headers, iterable $rows): Response
    {
        $xml = '<?xml version="1.0"?>'."\n"
            .'<?mso-application progid="Excel.Sheet"?>'."\n"
            .'<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">'
            .'<Styles><Style ss:ID="Header"><Font ss:Bold="1"/><Interior ss:Color="#F7F6F3" ss:Pattern="Solid"/></Style></Styles>'
            .'<Worksheet ss:Name="Sheet1"><Table>';

        $xml .= '<Row>';
        foreach ($headers as $head) {
            $xml .= '<Cell ss:StyleID="Header"><Data ss:Type="String">'.self::esc($head).'</Data></Cell>';
        }
        $xml .= '</Row>';

        foreach ($rows as $row) {
            $xml .= '<Row>';
            foreach ($row as $cell) {
                $xml .= '<Cell><Data ss:Type="String">'.self::esc((string) $cell).'</Data></Cell>';
            }
            $xml .= '</Row>';
        }

        $xml .= '</Table></Worksheet></Workbook>';

        return response($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private static function esc(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }
}
