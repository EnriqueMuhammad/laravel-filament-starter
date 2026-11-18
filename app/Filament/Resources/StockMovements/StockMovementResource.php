<?php

declare(strict_types=1);

namespace App\Filament\Resources\StockMovements;

use App\Enums\StockMovementType;
use App\Filament\Resources\StockMovements\Pages\CreateStockMovement;
use App\Filament\Resources\StockMovements\Pages\EditStockMovement;
use App\Filament\Resources\StockMovements\Pages\ListStockMovements;
use App\Models\StockMovement;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationLabel = 'Stock movements';

    protected static ?string $modelLabel = 'stock movement';

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Movement')
                ->description('Inbound and outbound quantities are positive. Adjustments may be negative.')
                ->schema([
                    Select::make('product_id')
                        ->label('Product')
                        ->relationship('product', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->native(false),
                    Select::make('type')
                        ->options(StockMovementType::class)
                        ->required()
                        ->native(false),
                    TextInput::make('quantity')
                        ->numeric()
                        ->required()
                        ->helperText('Use a negative number only for an adjustment that reduces stock.'),
                    DateTimePicker::make('occurred_at')
                        ->label('Occurred')
                        ->default(now())
                        ->required()
                        ->seconds(false),
                    Textarea::make('note')
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('occurred_at')
                    ->label('When')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.sku')
                    ->label('SKU')
                    ->toggleable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->formatStateUsing(fn (StockMovement $record): string => sprintf('%+d', $record->signedQuantity()))
                    ->color(fn (StockMovement $record): string => $record->signedQuantity() >= 0 ? 'success' : 'danger'),
                TextColumn::make('user.name')
                    ->label('Recorded by')
                    ->placeholder('System')
                    ->toggleable(),
                TextColumn::make('note')
                    ->limit(40)
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(StockMovementType::class),
                SelectFilter::make('product')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('occurred_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockMovements::route('/'),
            'create' => CreateStockMovement::route('/create'),
            'edit' => EditStockMovement::route('/{record}/edit'),
        ];
    }
}
