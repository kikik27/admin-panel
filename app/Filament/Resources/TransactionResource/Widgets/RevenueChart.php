<?php

namespace App\Filament\Resources\TransactionResource\Widgets;


use Filament\Forms\Components\Select;
use App\Models\Transaction;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Forms\Components\DatePicker;
class RevenueChart extends ChartWidget
{
    use HasFiltersAction;
    public function getHeading(): string
    {
        return 'Pendapatan bulanan (' . now()->translatedFormat('Y').')';
    }

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->form([
                    DatePicker::make('startDate'),
                    DatePicker::make('endDate'),
                    // ...
                ]),
        ];
    }

    protected function getData(): array
    {
        $data = Trend::model(Transaction::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count(); // pakai count() hanya untuk dapat range waktu

        $monthlyTotals = $data->map(function ($point) {
            $date = Carbon::parse($point->date);

            $transactions = Transaction::with('TransactionDetails')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->get();

            $total = $transactions->sum(function ($trx) {
                return $trx->amount;
            });

            return [
                'label' => $date->translatedFormat('F'),
                'value' => $total,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $monthlyTotals->pluck('value'),
                ],
            ],
            'labels' => $monthlyTotals->pluck('label'),
        ];
    }


    protected function getType(): string
    {
        return 'line';
    }


    protected function filterFormSchema(): array
    {
        return [
            Select::make('year')
                ->label('Tahun')
                ->options($this->getAvailableYears())
                ->default(now()->year)
                ->reactive(),
        ];
    }

    protected function getAvailableYears(): array
    {
        $minYear = Transaction::min('created_at')?->format('Y') ?? now()->year;

        return collect(range($minYear, now()->year))
            ->reverse()
            ->mapWithKeys(fn($year) => [$year => $year])
            ->toArray();
    }


}
