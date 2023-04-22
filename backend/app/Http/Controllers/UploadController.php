<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $file = $request->file('file');
        $now = Carbon::now()->getTimestamp();
        $fileName = "$now." . $file->getClientOriginalExtension();
        $path = "files/$fileName";
        Storage::disk('public')->put($path, $file->getContent());
        return $this->resourceItemResponse('url', Storage::disk('public')->url($path), [
            'path' => $path,
        ]);
    }
}
