<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                            ->label('Transaction Code')
                            ->disabled(),
                        Forms\Components\Select::make('delivery.name')
                            ->relationship('delivery', 'name')
                            ->label('Delivery')
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->disabled()
                            ->prefix('IDR'),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->disabled()
                            ->options([
                                'process' => 'Process',
                                'on_delivery' => 'On Delivery',
                                'complete' => 'Complete',
                                'cancled' => 'Canceled',
                            ]),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Created At')
                            ->disabled(),
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
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
}
