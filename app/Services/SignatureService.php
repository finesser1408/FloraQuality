<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SignatureService
{
    /**
     * Persist a base64-encoded PNG signature and return its storage path.
     * Returns null when the input is blank or not a valid PNG data URL.
     *
     * @param  string|null $base64DataUrl   e.g. "data:image/png;base64,iVBORw..."
     * @param  string      $prefix          Folder prefix, e.g. "staff" or "supplier"
     */
    public function store(?string $base64DataUrl, string $prefix = 'signature'): ?string
    {
        if (blank($base64DataUrl) || ! str_starts_with($base64DataUrl, 'data:image/png;base64,')) {
            return null;
        }

        $base64 = substr($base64DataUrl, strpos($base64DataUrl, ',') + 1);
        $imageData = base64_decode($base64);

        if ($imageData === false) {
            return null;
        }

        $filename = "signatures/{$prefix}/" . Str::uuid() . '.png';
        Storage::disk('public')->put($filename, $imageData);

        return $filename;
    }

    /**
     * Remove a stored signature file.
     */
    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
