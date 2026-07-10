<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => StoreSetting::deliverySettings(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'inside_dhaka_delivery_charge' => ['required', 'integer', 'min:0'],
            'outside_dhaka_delivery_charge' => ['required', 'integer', 'min:0'],
            'bkash_number' => ['required', 'string', 'max:30'],
            'nagad_number' => ['required', 'string', 'max:30'],
            'rocket_number' => ['required', 'string', 'max:30'],
            'delivery_pay_later_note_bn' => ['required', 'string', 'max:500'],
        ]);

        foreach ($data as $key => $value) {
            StoreSetting::setValue($key, (string) $value);
        }

        return back()->with('success', 'Store settings updated.');
    }
}
