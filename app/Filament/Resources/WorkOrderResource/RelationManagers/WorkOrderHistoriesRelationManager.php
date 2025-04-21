<?php

namespace App\Filament\Resources\WorkOrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkOrderHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'histories';

    protected static ?string $recordTitleAttribute = 'action';

    protected static ?string $title = '工单历史';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('action')
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('work-order-histories.user_id'))
                    ->sortable(),

                TextColumn::make('action')
                    ->label(__('work-order-histories.action'))
                    ->formatStateUsing(fn(string $state): string => __("work-order-histories.actions.{$state}"))
                    ->sortable(),

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
                    ->formatStateUsing(fn(string $state): string => $state ? __("work-orders.statuses.{$state}") : '')
                    ->label(__('work-order-histories.from_status')),

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
                    ->formatStateUsing(fn(string $state): string => $state ? __("work-orders.statuses.{$state}") : '')
                    ->label(__('work-order-histories.to_status')),

                TextColumn::make('comment')
                    ->label(__('work-order-histories.comment'))
                    ->limit(100)
                    ->wrap(),

                TextColumn::make('created_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->label(__('work-order-histories.created_at')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
