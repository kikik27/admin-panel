<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
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
                Forms\Components\Card::make()->schema([

                    Grid::make('3')->schema([
                        TextInput::make('transaction_code'),
                        TextInput::make('customer'),
                        TextInput::make('address'),
                        TextInput::make('phone'),
                        TextInput::make('status'),
                        TextInput::make('amount'),
                        Forms\Components\Select::make('delivery_id')
                            ->relationship('delivery', 'name')
                            ->label('Delivery')
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Total Amount')
                            ->numeric()
                            ->disabled(),
                    ]),
                    Grid::make('3')->schema([
                        Forms\Components\Repeater::make('transactionDetails')
                            ->relationship('TransactionDetails')
                            ->schema([
                                Forms\Components\Select::make('products_id')
                                    ->relationship('product', 'name')
                                    ->label('Product')
                                    ->required(),
                                Forms\Components\TextInput::make('qty')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->label('Transaction Details')
                            ->createItemButtonLabel('Add Product')
                            ->disabled(),
                    ])
                ])
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\TransactionDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}