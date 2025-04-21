<?php

namespace App\Filament\Resources\WorkOrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkOrderHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'histories';

    protected static ?string $recordTitleAttribute = 'action';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('action')
                    ->required()
                    ->maxLength(255)
                    ->label('操作'),
                Forms\Components\TextInput::make('from_status')
                    ->maxLength(255)
                    ->label('原状态'),
                Forms\Components\TextInput::make('to_status')
                    ->maxLength(255)
                    ->label('新状态'),
                Forms\Components\Textarea::make('comment')
                    ->columnSpanFull()
                    ->label('备注'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('action')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('操作人')
                    ->sortable(),
                Tables\Columns\TextColumn::make('action')
                    ->label('操作')
                    ->sortable(),
                Tables\Columns\TextColumn::make('from_status')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending_assignment' => '待指派',
                        'assigned' => '已指派',
                        'in_progress' => '维修中',
                        'pending_review' => '待审核',
                        'rejected' => '已驳回',
                        'completed' => '已完成',
                        'archived' => '已归档',
                        default => $state,
                    })
                    ->label('原状态'),
                Tables\Columns\TextColumn::make('to_status')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending_assignment' => '待指派',
                        'assigned' => '已指派',
                        'in_progress' => '维修中',
                        'pending_review' => '待审核',
                        'rejected' => '已驳回',
                        'completed' => '已完成',
                        'archived' => '已归档',
                        default => $state,
                    })
                    ->label('新状态'),
                Tables\Columns\TextColumn::make('comment')
                    ->label('备注')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('操作时间'),
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
