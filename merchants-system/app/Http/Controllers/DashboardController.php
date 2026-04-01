<?php

namespace App\Http\Controllers;

use App\Models\Merchant;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_merchants' => Merchant::count(),
            'contacted_merchants' => Merchant::where('contacted', 1)->count(),
            'invited_merchants' => Merchant::where('invited', 1)->count(),
            'not_contacted_merchants' => Merchant::where('contacted', 0)->count(),
            'not_invited_merchants' => Merchant::where('invited', 0)->count(),
            'with_notes' => Merchant::whereNotNull('notes')->where('notes', '!=', '')->count(),
            'without_notes' => Merchant::where(function ($q) {
                $q->whereNull('notes')->orWhere('notes', '');
            })->count(),
            'latest_merchants' => Merchant::latest()->take(10)->get(),
            'sector_counts' => Merchant::selectRaw('sector, COUNT(*) as total')
                ->groupBy('sector')
                ->orderByDesc('total')
                ->take(10)
                ->get(),
        ];

        return view('dashboard.index', compact('stats'));
    }
}
