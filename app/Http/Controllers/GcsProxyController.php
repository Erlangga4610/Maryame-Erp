<?php

namespace App\Http\Controllers;

use App\Helpers\StorageHelper;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class GcsProxyController extends Controller
{
    public function __invoke(string $path)
    {
        if (! StorageHelper::exists($path)) {
            abort(404);
        }

        $content = Storage::disk('gcs')->get($path);
        $mime = Storage::disk('gcs')->mimeType($path) ?? 'application/octet-stream';
        $size = Storage::disk('gcs')->size($path);

        return Response::stream(function () use ($content) {
            echo $content;
        }, 200, [
            'Content-Type' => $mime,
            'Content-Length' => $size,
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }
}
