<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Grid::make(3)->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Produk')
                            ->required()
                            ->unique(ignorable: fn($record) => $record),

                        Forms\Components\TextInput::make('price')
                            ->label('Harga')
                            ->numeric()
                            ->required(),
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->required()
                            ->default('product')
                            ->options(
                                ['product' => 'Product', 'packaging' => 'Packing']
                            ),
                    ])
                    ,
                    RichEditor::make('description')->toolbarButtons([
                        'blockquote',
                        'bold',
                        'bulletList',
                        'h2',
                        'h3',
                        'italic',
                        'orderedList',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                    ]),
                    Forms\Components\Repeater::make('catalogImages')
                        ->grid([
                            "xl" => 2,
                            "lg" => 2,
                            "md" => 2,
                            "sm" => 1,
                            "xs" => 1,
                        ])
                        ->label('Gambar Produk')
                        ->relationship('catalogImages')
                        ->schema([
                            Forms\Components\FileUpload::make('image')
                                ->label('Gambar')
                                ->directory('products')
                        ])
                        ->minItems(0)
                        ->createItemButtonLabel('Tambah Gambar'),
                ])
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR', true)
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Status'),
            ])
            ->filters(
                [
                    Tables\Filters\SelectFilter::make('is_active')
                        ->label('Status')
                        ->options([
                            1 => 'Active',
                            0 => 'Inactive',
                        ]),
                    SelectFilter::make('category')
                        ->label('Category')
                        ->options(
                            ['product' => 'Product', 'packaging' => 'Packing']
                        )
                ]
            )
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}