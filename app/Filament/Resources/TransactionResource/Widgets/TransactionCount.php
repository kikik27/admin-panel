<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Filament\Resources\TransactionResource\Pages\ListTransactions;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;

class TransactionCount extends BaseWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListTransactions::class;
    }

    protected function getStats(): array
    {
        $query = $this->getPageTableQuery();

        return [
            Stat::make('Total Transaksi', $query->count())
                ->icon('heroicon-o-shopping-cart')
                ->color('gray'),

            Stat::make('Diproses', $query->clone()->where('status', 'process')->count())
                ->icon('heroicon-o-arrow-path')
                ->color('warning'),

            Stat::make('Dalam Pengiriman', $query->clone()->where('status', 'on_delivery')->count())
                ->icon('heroicon-o-truck')
                ->color('info'),

            Stat::make('Selesai', $query->clone()->where('status', 'complete')->count())
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Dibatalkan', $query->clone()->where('status', 'cancled')->count())
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}
