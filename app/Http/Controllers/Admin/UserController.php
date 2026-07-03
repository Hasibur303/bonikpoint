<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::latest()->paginate(12),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate(['utype' => ['required', 'in:usr,adm']]);
        $user->update(['utype' => $request->utype]);

        return back()->with('success', 'User role updated.');
    }
}
