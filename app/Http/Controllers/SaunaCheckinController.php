<?php

namespace App\Http\Controllers;

use App\Models\Sauna;
use Illuminate\Contracts\View\View;

class SaunaCheckinController extends Controller
{
    /**
     * Static iPad URL for a sauna. The active manifest is resolved client-side
     * via /api/sauna/{slug}/active so it can rotate without a page reload.
     */
    public function __invoke(Sauna $sauna): View
    {
        return view('checkin', [
            'saunaSlug' => $sauna->slug,
            'saunaName' => $sauna->name,
        ]);
    }
}
