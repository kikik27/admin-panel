<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Blade;
use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Invoice')
                ->icon('heroicon-c-printer')
                ->action(function (Transaction $record) {
                    try {
                        // Generate PDF invoice
                        $pdf = Pdf::loadHtml(
                            Blade::render('invoice', ['transaction' => $record])
                        );

                        // Create directory if it doesn't exist
                        $directory = storage_path('app/private/invoices');
                        if (!file_exists($directory)) {
                            mkdir($directory, 0755, true);
                        }

                        // Save PDF to storage with proper path
                        $pdfFileName = $record->transaction_code . '.pdf';
                        $pdfPath = 'invoices/' . $pdfFileName;
                        Storage::put($pdfPath, $pdf->output());

                        // Send to API
                        $this->sendMediaToAPI($record, $pdfPath);
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error')
                            ->body('Failed to generate or save invoice: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    protected function sendMediaToAPI(Transaction $record, string $pdfPath): void
    {
        try {
            $url = config('services.wa.url');
            $apiKey = config('services.wa.key');
            $deviceId = config('services.wa.device');

            $fullPath = storage_path('app/private/' . $pdfPath);

            if (!file_exists($fullPath)) {
                throw new \Exception('PDF file not found at: ' . $fullPath);
            }

            $caption = "Pesanan Anda telah kami proses. Terima kasih telah melakukan pemesanan dengan nomor invoice {$record->transaction_code}.\n\n";
            $caption .= "Mohon segera lakukan pembayaran sesuai dengan invoice yang terlampir.\n";
            $caption .= "Jika sudah melakukan pembayaran, mohon konfirmasi dengan mengirimkan bukti transfer.";
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'X-API-KEY' => $apiKey,
                'Accept' => 'application/json',
            ])->attach(
                    'file',
                    file_get_contents($fullPath),
                    $record->transaction_code . '.pdf'
                )->post($url, [
                        'caption' => $caption,
                        'target' => $record->phone. "@c.us",
                        'deviceId' => $deviceId
                    ]);

            if ($response->successful()) {
                Notification::make()
                    ->title('Success')
                    ->body('Invoice sent successfully!')
                    ->success()
                    ->send();
            } else {
                throw new \Exception('API responded with: ' . $response->body());
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Failed to send invoice: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}