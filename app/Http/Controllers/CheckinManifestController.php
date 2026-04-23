<?php

namespace App\Http\Controllers;

use App\Models\Manifest;
use Illuminate\Contracts\View\View;

class CheckinManifestController extends Controller
{
    /**
     * Kiosk view filtered to a single manifest (must exist in DB).
     */
    public function __invoke(string $manifestId): View
    {
        $manifest = Manifest::query()->where('manifest_id', $manifestId)->firstOrFail();

        return view('checkin', [
            'manifestId' => $manifest->manifest_id,
            'manifestName' => $manifest->name,
        ]);
    }
}
