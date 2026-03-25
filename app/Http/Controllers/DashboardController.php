<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLogs;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $weekStart = Carbon::today()->subDays(6);

        $todayVisitors = Visitor::query()
            ->whereDate(DB::raw('COALESCE(time_in, created_at)'), $today)
            ->count();

        $visitorsIn = Visitor::query()->where('status', 0)->count();
        $visitorsOut = Visitor::query()->where('status', 1)->count();

        $todayEmployeeLogs = EmployeeLogs::query()
            ->whereDate('time', $today)
            ->count();

        $visitorTypeBreakdown = DB::table('visitor_types as vt')
            ->leftJoin('visitors as v', function ($join) use ($today) {
                $join->on('v.visitor_type', '=', 'vt.id')
                    ->whereNull('v.deleted_at')
                    ->whereDate(DB::raw('COALESCE(v.time_in, v.created_at)'), $today);
            })
            ->whereNull('vt.deleted_at')
            ->groupBy('vt.id', 'vt.name')
            ->select('vt.name', DB::raw('COUNT(v.id) as total'))
            ->orderByDesc('total')
            ->get();

        $weeklyRaw = Visitor::query()
            ->selectRaw('DATE(COALESCE(time_in, created_at)) as day, COUNT(*) as total')
            ->whereDate(DB::raw('COALESCE(time_in, created_at)'), '>=', $weekStart)
            ->groupBy('day')
            ->pluck('total', 'day');

        $weeklyTrend = collect(range(0, 6))->map(function ($dayOffset) use ($weekStart, $weeklyRaw) {
            $date = $weekStart->copy()->addDays($dayOffset);
            $key = $date->toDateString();

            return [
                'label' => $date->format('D'),
                'full_date' => $date->format('M d'),
                'total' => (int) ($weeklyRaw[$key] ?? 0),
            ];
        });

        $maxTypeCount = max(1, (int) $visitorTypeBreakdown->max('total'));
        $maxTrendCount = max(1, (int) $weeklyTrend->max('total'));

        return view('pages.dashboard.dashboard', [
            'todayVisitors' => $todayVisitors,
            'visitorsIn' => $visitorsIn,
            'visitorsOut' => $visitorsOut,
            'todayEmployeeLogs' => $todayEmployeeLogs,
            'visitorTypeBreakdown' => $visitorTypeBreakdown,
            'weeklyTrend' => $weeklyTrend,
            'maxTypeCount' => $maxTypeCount,
            'maxTrendCount' => $maxTrendCount,
        ]);
    }
}
