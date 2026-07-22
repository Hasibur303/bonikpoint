<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('orders.index');
    }
}
