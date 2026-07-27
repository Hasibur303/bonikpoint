<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfitController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->input('filter', 'month');

        if (! in_array($filter, ['day', 'month', 'year', 'all'], true)) {
            $filter = 'month';
        }

        [$start, $end, $periodLabel] = $this->period($filter, $request);

        $baseQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->where(function ($query) {
                $query->where('orders.status', 'delivered')
                    ->orWhere(function ($offlineSales) {
                        $offlineSales->where('orders.is_offline_sale', true)
                            ->where('orders.status', '!=', 'cancelled');
                    });
            })
            ->when($start && $end, fn ($query) => $query->whereBetween('orders.created_at', [$start, $end]));

        $costExpression = 'COALESCE(order_items.buying_price, products.buying_price, 0)';

        $summary = (clone $baseQuery)
            ->selectRaw('COALESCE(SUM(order_items.total), 0) as revenue')
            ->selectRaw("COALESCE(SUM({$costExpression} * order_items.quantity), 0) as cost")
            ->selectRaw("COALESCE(SUM(order_items.total - ({$costExpression} * order_items.quantity)), 0) as profit")
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0) as units')
            ->selectRaw('COUNT(DISTINCT orders.id) as orders_count')
            ->first();

        $products = (clone $baseQuery)
            ->select('order_items.product_name')
            ->selectRaw('SUM(order_items.quantity) as quantity_sold')
            ->selectRaw('SUM(order_items.total) as revenue')
            ->selectRaw("SUM({$costExpression} * order_items.quantity) as cost")
            ->selectRaw("SUM(order_items.total - ({$costExpression} * order_items.quantity)) as profit")
            ->groupBy('order_items.product_name')
            ->orderByDesc('profit')
            ->get();

        return view('admin.profit.index', [
            'filter' => $filter,
            'periodLabel' => $periodLabel,
            'summary' => $summary,
            'products' => $products,
            'date' => $request->input('date', now()->toDateString()),
            'month' => $request->input('month', now()->format('Y-m')),
            'year' => $request->input('year', now()->year),
        ]);
    }

    private function period(string $filter, Request $request): array
    {
        if ($filter === 'all') {
            return [null, null, 'All Time'];
        }

        if ($filter === 'day') {
            $date = $this->safeDate($request->input('date'), now()->toDateString());

            return [$date->startOfDay(), $date->endOfDay(), $date->format('d M Y')];
        }

        if ($filter === 'year') {
            $year = (int) $request->input('year', now()->year);
            $year = max(2000, min(2100, $year));
            $date = CarbonImmutable::create($year, 1, 1);

            return [$date->startOfYear(), $date->endOfYear(), (string) $year];
        }

        $month = $this->safeMonth($request->input('month'), now()->format('Y-m'));

        return [$month->startOfMonth(), $month->endOfMonth(), $month->format('F Y')];
    }

    private function safeDate(?string $value, string $fallback): CarbonImmutable
    {
        try {
            return CarbonImmutable::parse($value ?: $fallback);
        } catch (\Throwable) {
            return CarbonImmutable::parse($fallback);
        }
    }

    private function safeMonth(?string $value, string $fallback): CarbonImmutable
    {
        try {
            return CarbonImmutable::createFromFormat('Y-m', $value ?: $fallback)->startOfMonth();
        } catch (\Throwable) {
            return CarbonImmutable::createFromFormat('Y-m', $fallback)->startOfMonth();
        }
    }
}
