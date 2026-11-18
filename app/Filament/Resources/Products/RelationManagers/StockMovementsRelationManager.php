<?php

declare(strict_types=1);

namespace App\Filament\Resources\Products\RelationManagers;

use App\Enums\StockMovementType;
use App\Filament\Resources\StockMovements\StockMovementResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class StockMovementsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockMovements';

    protected static ?string $title = 'Stock movements';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Movement')
                ->schema([
                    Select::make('type')
                        ->options(StockMovementType::class)
                        ->required()
                        ->native(false),
                    TextInput::make('quantity')
                        ->numeric()
                        ->required(),
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

    public function table(Table $table): Table
    {
        return StockMovementResource::table($table)
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
