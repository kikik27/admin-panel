<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationGroup = 'Transactions';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\TextInput::make('transaction_code')
                            ->label('Transaction Code'),
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->prefix('IDR'),
                        Forms\Components\TextInput::make('customer')->label('Customer'),
                        Forms\Components\Textarea::make('address')->label('Address'),
                        Forms\Components\TextInput::make('phone')->label('Phone'),
                        Forms\Components\Select::make('delivery.name')
                            ->relationship('delivery', 'name')
                            ->label('Delivery')
                        ,
                        Forms\Components\TextInput::make('delivery_fee')
                            ->label('Delivery Fee')
                            ->prefix('IDR')
                            ->numeric()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status')

                            ->options([
                                'process' => 'Process',
                                'on_delivery' => 'On Delivery',
                                'complete' => 'Complete',
                                'cancled' => 'Canceled',
                            ]),
                        Forms\Components\DateTimePicker::make('created_at')->label('Order Date')
                        ,
                    ])
                    ->columns(2),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_code')
                    ->label('Transaction Code')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('delivery.name')
                    ->label('Delivery'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('IDR', true),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'on_delivery',
                        'success' => 'complete',
                        'danger' => 'cancled',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'on_delivery' => 'On Delivery',
                        'complete' => 'Complete',
                        'cancled' => 'Canceled',
                        default => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Transaction Status')->options([

                ]),
            ])
            ->actions([

                Tables\Actions\Action::make('Confirm')
                    ->visible(fn(Transaction $record) => $record->status === 'process')
                    ->label('Confirm Transaction')
                    ->icon('heroicon-m-check-circle')
                    ->form([
                        Forms\Components\TextInput::make('delivery_fee')
                            ->label('Delivery Fee')
                            ->numeric()
                            ->required()
                    ])

                    ->action(function (Transaction $record, array $data) {
                        $record->update(['delivery_fee' => $data['delivery_fee'], 'status' => 'on_delivery']);

                        Notification::make()
                            ->title('Transcation ' . $record->transaction_code)
                            ->body("Status changed to On Delivery")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('Complete')
                    ->label('Transaction Complete')
                    ->icon('heroicon-m-check-circle')
                    ->visible(fn(Transaction $record) => $record->status === 'on_delivery') // Hanya muncul jika status 'on_delivery'
                    ->requiresConfirmation() // Konfirmasi sebelum mengubah status
                    ->action(function (Transaction $record) {
                        $record->update(['status' => 'complete']);

                        Notification::make()
                            ->title('Transaction ' . $record->transaction_code)
                            ->body("Status changed to Complete")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('Invoice')->icon('heroicon-c-printer')->visible(fn(Transaction $record) => $record->status != 'process')
                    ->action(
                        function (Transaction $record) {
                            return response()->streamDownload(function () use ($record) {
                                echo Pdf::loadHtml(
                                    Blade::render('invoice', ['transaction' => $record])
                                )->stream();
                            }, 'C&J-Invoice-' . $record->transaction_code . '.pdf');

                        }
                    ),
                // Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TransactionDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'view' => Pages\ViewTransaction::route('/{record}/view'),
        ];
    }

    public static function getTableActions(): array
    {
        return [
            Tables\Actions\ViewAction::make(),
            Tables\Actions\Action::make('process')
                ->label('Process Transaction')
                ->color('primary')
                ->icon('heroicon-o-truck')
                ->form([
                    Forms\Components\TextInput::make('delivery_fee')
                        ->label('Delivery Fee')
                        ->required()
                        ->numeric()
                        ->prefix('IDR')
                ])
                ->action(function (Transaction $record, array $data) {
                    try {
                        $record->processTransaction($data['delivery_fee']);

                        Notification::make()
                            ->title('Transaction Processed')
                            ->body("Transaction {$record->transaction_code} is now on delivery")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Processing Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn(Transaction $record) => $record->status === 'process')
        ];
    }
}
