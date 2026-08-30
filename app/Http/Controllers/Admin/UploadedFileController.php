<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class UploadedFileController extends Controller
{
    public function view(UploadedFile $uploadedFile): Response
    {
        if (! Storage::disk('public')->exists($uploadedFile->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $fullPath = Storage::disk('public')->path($uploadedFile->file_path);

        return response()->file($fullPath, [
            'Content-Type' => $uploadedFile->mime_type,
            'Content-Disposition' => 'inline; filename="'.$uploadedFile->file_name.'"',
        ]);
    }

    public function download(UploadedFile $uploadedFile): Response
    {
        if (! Storage::disk('public')->exists($uploadedFile->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $fullPath = Storage::disk('public')->path($uploadedFile->file_path);

        return response()->download($fullPath, $uploadedFile->file_name);
    }
}
