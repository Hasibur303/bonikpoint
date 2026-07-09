<?php

namespace App\Http\Controllers;

use App\Models\Festival;
use Illuminate\View\View;

class FestivalController extends Controller
{
    public function show(Festival $festival): View
    {
        abort_unless($festival->isRunning(), 404);

        return view('festivals.show', [
            'festival' => $festival->load(['products' => fn ($query) => $query->where('is_active', true)->with('category')]),
        ]);
    }
}
