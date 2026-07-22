<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::where('utype', 'usr')->latest()->paginate(12),
        ]);
    }

    public function admins(): View
    {
        return view('admin.users.admins', [
            'admins' => User::where('utype', 'adm')->latest()->paginate(12),
        ]);
    }

    public function createAdmin(): View
    {
        return view('admin.users.create-admin');
    }

    public function storeAdmin(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'mobile' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'] ?? null,
            'password' => Hash::make($data['password']),
            'utype' => 'adm',
        ]);

        return redirect()->route('admin.users.admins')->with('success', 'Administrator account created.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate(['utype' => ['required', 'in:usr']]);

        if ($user->utype !== 'adm') {
            return back()->withErrors([
                'utype' => 'Customer accounts cannot be promoted from this page. Use Add Administrator for admin access.',
            ]);
        }

        if ($user->utype === 'adm' && $request->utype !== 'adm' && User::where('utype', 'adm')->count() <= 1) {
            return back()->withErrors([
                'utype' => 'At least one administrator must remain active.',
            ]);
        }

        if ($user->is(auth()->user()) && $request->utype !== 'adm') {
            return back()->withErrors([
                'utype' => 'You cannot remove your own administrator access.',
            ]);
        }

        $user->update(['utype' => $request->utype]);

        return back()->with('success', 'User role updated.');
    }
}
