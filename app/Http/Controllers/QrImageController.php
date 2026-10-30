<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class QrImageController extends Controller
{
    /**
     * Serve a QR code image by reference number.
     * Reads from whichever disk QR_DISK is configured to (public or r2),
     * so it works both locally and in production without the bucket needing
     * to be publicly accessible.
     */
    public function show(string $reference, string $token): Response
    {
        $registration = Registration::query()
            ->where('reference', $reference)
            ->where('qr_token', $token)
            ->whereNotNull('qr_code_path')
            ->firstOrFail();

        $path = $registration->qr_code_path;
        $disk = config('filesystems.qr_disk', 'r2');

        if (! Storage::disk($disk)->exists($path)) {
            abort(404);
        }

        $image = Storage::disk($disk)->get($path);

        return response($image, 200)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
