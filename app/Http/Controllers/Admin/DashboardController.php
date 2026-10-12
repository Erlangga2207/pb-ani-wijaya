<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, DashboardService $dashboard): View
    {
        // Data keuangan tidak di-query sama sekali untuk staff, bukan cuma disembunyikan di Blade.
        $isOwner = $request->user()->isOwnerOrAbove();

        return view('admin.dashboard', [
            'items' => $dashboard->activeItems(),
            'today' => $dashboard->todayActivity(),
            'yieldTrend' => $dashboard->yieldTrend(),
            'finance' => $isOwner ? $dashboard->finance() : null,
            'salesTrend' => $isOwner ? $dashboard->salesTrend() : null,
            'overdue' => $isOwner ? $dashboard->overdue() : null,
        ]);
    }
}
