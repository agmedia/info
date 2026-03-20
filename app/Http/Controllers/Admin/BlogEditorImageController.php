<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogEditorImageController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'file', 'image', 'max:12288'],
        ]);

        $file = $validated['image'];
        $originalName = (string) pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = Str::slug($originalName) ?: 'blog-image';
        $extension = strtolower((string) ($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg'));
        $directory = 'blog/editor-images/'.now()->format('Y/m');
        $fileName = $safeBaseName.'-'.Str::lower(Str::random(8)).'.'.$extension;
        $path = $file->storeAs($directory, $fileName, 'public');

        return response()->json([
            'ok' => true,
            'name' => $originalName !== '' ? $originalName : $safeBaseName,
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ], 201);
    }
}
