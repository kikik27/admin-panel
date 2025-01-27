<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Invoice')->icon('heroicon-c-printer')->action(
                function (Transaction $record) {
                    return response()->streamDownload(function () use ($record) {
                        echo Pdf::loadHtml(
                            Blade::render('invoice')
                        )->stream();
                    },'test.pdf');
                }
            ),
        ];
    }

    // protected function getAc(): array
    // {
    //     return [
    //         'transaction' => $this->record,
    //     ];
    // }
}
