<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content\Support\CareerApplication;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CareerApplicationDocumentController extends Controller
{
    public function __invoke(CareerApplication $careerApplication): StreamedResponse
    {
        abort_if(trim((string) $careerApplication->cv_path) === '', 404);

        $disk = trim((string) ($careerApplication->cv_disk ?: CareerApplication::CV_DISK));
        abort_unless(Storage::disk($disk)->exists($careerApplication->cv_path), 404);

        return Storage::disk($disk)->download(
            $careerApplication->cv_path,
            $careerApplication->downloadName()
        );
    }
}
