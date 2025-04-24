<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\TransactionDetail;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PopularProductsChart extends ChartWidget
{
    public function getHeading(): string
    {
        return 'Produk Terlaris (' . now()->translatedFormat('F Y') . ')';
    }
    protected static ?int $sort = 1;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        $topProducts = TransactionDetail::whereHas('transaction', function ($query) use ($month, $year) {
            $query->whereMonth('created_at', $month)
                ->whereYear('created_at', $year);
        })
            ->select('product_id', DB::raw('SUM(qty) as total_qty'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->product->name => $item->total_qty];
            });

        return [
            'datasets' => [
                [
                    'data' => $topProducts->values()->toArray(),
                    'backgroundColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                    ],
                ]
            ],
            'labels' => $topProducts->keys()->toArray(),
        ];
    }
}
