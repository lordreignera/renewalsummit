<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Payment;
use App\Models\Registration;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total'         => Registration::whereIn('status', ['paid', 'checked_in'])->count(),
            'checked_in'    => Registration::where('status', 'checked_in')->count(),
            'pending'       => Registration::where('status', 'pending')->count(),
            'draft'         => Registration::where('status', 'draft')->count(),
            'fcc'           => Registration::where('affiliation', 'fcc')->whereIn('status', ['paid', 'checked_in'])->count(),
            'international' => Registration::where('country_type', 'international')->whereIn('status', ['paid', 'checked_in'])->count(),
            'local'         => Registration::where('country_type', 'local')->whereIn('status', ['paid', 'checked_in'])->count(),
            'revenue'       => Payment::where('status', 'success')->sum('amount'),
            'donations'     => Donation::where('status', 'success')->sum('amount'),
        ];

        // Daily new registrations (last 7 days)
        $daily = Registration::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereIn('status', ['paid', 'checked_in'])
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        // Today's registrations
        $todayRegistrations = Registration::whereDate('created_at', today())
            ->whereIn('status', ['paid', 'checked_in'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'daily', 'todayRegistrations'));
    }
}
