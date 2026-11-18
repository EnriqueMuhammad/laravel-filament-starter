<?php

declare(strict_types=1);

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\RelationManagers\StockMovementsRelationManager;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationLabel = 'Products';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Product')
                ->schema([
                    Select::make('category_id')
                        ->label('Category')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->native(false),
                    TextInput::make('sku')
                        ->label('SKU')
                        ->required()
                        ->maxLength(40)
                        ->unique(ignoreRecord: true)
                        ->dehydrateStateUsing(fn (?string $state): ?string => $state === null ? null : strtoupper($state)),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(160)
                        ->columnSpanFull(),
                    Textarea::make('description')
                        ->rows(3)
                        ->columnSpanFull(),
                    TextInput::make('unit_price')
                        ->label('Unit price')
                        ->prefix('$')
                        ->numeric()
                        ->minValue(0)
                        ->step(0.01)
                        ->required(),
                    TextInput::make('reorder_level')
                        ->label('Reorder level')
                        ->numeric()
                        ->minValue(0)
                        ->required()
                        ->helperText('The on-hand badge turns amber at or below this number.'),
                    TextInput::make('quantity_on_hand')
                        ->label('On hand')
                        ->numeric()
                        ->disabled()
                        ->dehydrated(false)
                        ->helperText('Change stock from a movement, not from this field.'),
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->helperText('Archived products stay in the ledger and drop off the active totals.'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                TextColumn::make('unit_price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('quantity_on_hand')
                    ->label('On hand')
                    ->sortable()
                    ->badge()
                    ->color(fn (Product $record): string => $record->isLowStock() ? 'warning' : 'gray'),
                TextColumn::make('reorder_level')
                    ->label('Reorder')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
                TernaryFilter::make('is_active')
                    ->label('Active')
                    ->trueLabel('Active')
                    ->falseLabel('Archived'),
                Filter::make('low_stock')
                    ->label('Low stock')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query
                        ->where('is_active', true)
                        ->whereColumn('quantity_on_hand', '<=', 'reorder_level')),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            StockMovementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Product::query()->lowStock()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}
