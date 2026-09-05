<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExport
{
    /**
     * Stream a CSV file as a download response.
     *
     * @param  array<int, string>  $headers  Column headers.
     * @param  iterable<array<int, mixed>>  $rows  Each item is an array of column values, in the same order as $headers.
     */
    public static function download(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');

            // BOM para que Excel detecte UTF-8 correctamente (acentos, ñ, etc.).
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
