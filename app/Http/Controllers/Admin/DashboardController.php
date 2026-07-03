<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'users' => User::count(),
                'admins' => User::where('role', UserRole::Admin)->count(),
                'recent' => User::latest()->take(5)->get(),
            ],
        ]);
    }
}
