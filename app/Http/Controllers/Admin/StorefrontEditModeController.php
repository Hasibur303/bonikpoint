<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StorefrontEditModeController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validate(['enabled' => ['required', 'boolean']]);
        $request->session()->put('storefront_edit_mode', (bool) $data['enabled']);

        return back();
    }
}
