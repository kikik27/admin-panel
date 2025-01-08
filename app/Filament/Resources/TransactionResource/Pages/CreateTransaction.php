<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $transaction = $this->record;

        foreach ($this->data['transactionDetails'] as $detail) {
            $transaction->details()->create([
                'product_id' => $detail['product_id'],
                'qty' => $detail['qty'],
                'amount' => $detail['amount'],
            ]);
        }

        $this->notify('success', 'Transaction and details created successfully!');
        $this->redirect($this->getResource()::getUrl('index'));
    }

}