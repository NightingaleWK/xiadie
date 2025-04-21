<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkOrderHistoryResource\Pages;
use App\Filament\Resources\WorkOrderHistoryResource\RelationManagers;
use App\Models\WorkOrderHistory;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkOrderHistoryResource extends Resource
{
    protected static ?string $model = WorkOrderHistory::class;
    protected static ?string $modelLabel = '工单历史';
    protected static ?string $navigationGroup = '维修';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('work_order_id')
                    ->relationship('workOrder', 'title')
                    ->required()
                    ->label(__('work-order-histories.work_order_id')),

                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->label(__('work-order-histories.user_id')),

                TextInput::make('action')
                    ->required()
                    ->maxLength(255)
                    ->label(__('work-order-histories.action')),

                TextInput::make('from_status')
                    ->maxLength(255)
                    ->label(__('work-order-histories.from_status')),

                TextInput::make('to_status')
                    ->maxLength(255)
                    ->label(__('work-order-histories.to_status')),

                Textarea::make('comment')
                    ->columnSpanFull()
                    ->label(__('work-order-histories.comment')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),

                TextColumn::make('workOrder.title')
                    ->label(__('work-orders.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label(__('work-order-histories.user_id'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('action')
                    ->formatStateUsing(fn(string $state): string => __("work-order-histories.actions.{$state}"))
                    ->label(__('work-order-histories.action'))
                    ->searchable(),

                TextColumn::make('from_status')
                    ->badge()
                    ->colors([
                        'info' => 'pending_assignment',
                        'primary' => 'assigned',
                        'warning' => 'in_progress',
                        'dark' => 'pending_review',
                        'danger' => 'rejected',
                        'success' => 'completed',
                        'secondary' => 'archived',
                    ])
                    ->formatStateUsing(fn(?string $state): string => $state ? __("work-orders.statuses.{$state}") : '')
                    ->label(__('work-order-histories.from_status'))
                    ->searchable(),

                TextColumn::make('to_status')
                    ->badge()
                    ->colors([
                        'info' => 'pending_assignment',
                        'primary' => 'assigned',
                        'warning' => 'in_progress',
                        'dark' => 'pending_review',
                        'danger' => 'rejected',
                        'success' => 'completed',
                        'secondary' => 'archived',
                    ])
                    ->formatStateUsing(fn(?string $state): string => $state ? __("work-orders.statuses.{$state}") : '')
                    ->label(__('work-order-histories.to_status'))
                    ->searchable(),

                TextColumn::make('comment')
                    ->label(__('work-order-histories.comment'))
                    ->limit(100)
                    ->wrap(),

                TextColumn::make('created_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->label(__('work-order-histories.created_at'))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->label(__('users.updated_at'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListWorkOrderHistories::route('/'),
            'create' => Pages\CreateWorkOrderHistory::route('/create'),
            'view' => Pages\ViewWorkOrderHistory::route('/{record}'),
            'edit' => Pages\EditWorkOrderHistory::route('/{record}/edit'),
        ];
    }
}
