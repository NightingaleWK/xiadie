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
                Forms\Components\Section::make()
                    ->schema([
                        TextInput::make('action')
                            ->label(__('work-order-histories.action'))
                            ->disabled()
                            ->formatStateUsing(function ($state) {
                                return __("work-order-histories.actions.{$state}");
                            }),

                        TextInput::make('user_name')
                            ->label(__('work-order-histories.user_id'))
                            ->disabled()
                            ->formatStateUsing(function ($state, $record) {
                                return $record->user ? $record->user->name : '';
                            }),

                        TextInput::make('from_status')
                            ->label(__('work-order-histories.from_status'))
                            ->disabled()
                            ->formatStateUsing(function ($state) {
                                return $state ? __("work-orders.statuses.{$state}") : '';
                            }),

                        TextInput::make('to_status')
                            ->label(__('work-order-histories.to_status'))
                            ->disabled()
                            ->formatStateUsing(function ($state) {
                                return $state ? __("work-orders.statuses.{$state}") : '';
                            }),

                        Textarea::make('comment')
                            ->label(__('work-order-histories.comment'))
                            ->disabled()
                            ->autosize()
                            ->columnSpanFull(),

                        TextInput::make('created_at')
                            ->label(__('work-order-histories.created_at'))
                            ->disabled()
                            ->formatStateUsing(fn($state) => $state ? date('Y-m-d H:i:s', strtotime($state)) : ''),
                    ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('action')
            ->columns([
                TextColumn::make('id'),

                TextColumn::make('user.name')
                    ->label(__('work-order-histories.user_id'))
                    ->sortable(),

                TextColumn::make('action')
                    ->label(__('work-order-histories.action'))
                    ->formatStateUsing(fn(string $state): string => __("work-order-histories.actions.{$state}"))
                    ->sortable(),

                TextColumn::make('from_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending_assignment' => 'sky',
                        'assigned' => 'purple',
                        'in_progress' => 'orange',
                        'pending_review' => 'indigo',
                        'rejected' => 'danger',
                        'completed' => 'success',
                        'archived' => 'slate',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => $state ? __("work-orders.statuses.{$state}") : '')
                    ->label(__('work-order-histories.from_status')),

                TextColumn::make('to_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending_assignment' => 'sky',
                        'assigned' => 'purple',
                        'in_progress' => 'orange',
                        'pending_review' => 'indigo',
                        'rejected' => 'danger',
                        'completed' => 'success',
                        'archived' => 'slate',
                        default => 'gray',
                    })
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
