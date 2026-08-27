<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

abstract class Controller
{
    protected function serveDocument(Request $request, ?string $storedPath): BinaryFileResponse
    {
        abort_unless($storedPath, 404, 'Dokumen tidak ditemukan.');

        $path = $this->normalizePublicPath($storedPath);
        abort_unless(Storage::disk('public')->exists($path), 404, 'File dokumen tidak ditemukan di penyimpanan.');

        $absolutePath = Storage::disk('public')->path($path);
        $mime = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';
        $fileName = basename($path);

        if ($request->boolean('download')) {
            return response()->download($absolutePath, $fileName);
        }

        return response()->file($absolutePath, [
            'Content-Type' => $mime,
            'Cache-Control' => 'no-cache, private',
        ]);
    }

    protected function normalizePublicPath(string $path): string
    {
        $path = str_replace('\\', '/', trim($path));
        $path = preg_replace('#^https?://[^/]+/storage/#i', '', $path) ?? $path;
        $path = preg_replace('#^/?storage/#i', '', $path) ?? $path;

        return ltrim($path, '/');
    }
}
