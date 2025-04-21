<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkOrderResource\Pages;
use App\Filament\Resources\WorkOrderResource\RelationManagers;
use App\Models\WorkOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Section;

class WorkOrderResource extends Resource
{
    protected static ?string $model = WorkOrder::class;
    protected static ?string $modelLabel = '工单';
    protected static ?string $navigationGroup = '维修';
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('基本信息')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->label('标题'),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->columnSpanFull()
                            ->label('描述'),
                    ])->columns(1),

                Section::make('状态信息')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending_assignment' => '待指派',
                                'assigned' => '已指派',
                                'in_progress' => '维修中',
                                'pending_review' => '待审核',
                                'rejected' => '已驳回',
                                'completed' => '已完成',
                                'archived' => '已归档',
                            ])
                            ->required()
                            ->label('状态'),
                        Select::make('creator_user_id')
                            ->relationship('creator', 'name')
                            ->required()
                            ->label('创建人'),
                        Select::make('assigned_user_id')
                            ->relationship('assignedUser', 'name')
                            ->label('指派人'),
                        Select::make('reviewer_user_id')
                            ->relationship('reviewer', 'name')
                            ->label('审核人'),
                    ])->columns(2),

                Section::make('维修信息')
                    ->schema([
                        Textarea::make('repair_details')
                            ->columnSpanFull()
                            ->label('维修详情'),
                        Textarea::make('rejection_reason')
                            ->columnSpanFull()
                            ->label('驳回原因'),
                        DateTimePicker::make('completed_at')
                            ->label('完成时间'),
                        DateTimePicker::make('archived_at')
                            ->label('归档时间'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),

                TextColumn::make('title')
                    ->searchable()
                    ->label('标题'),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending_assignment',
                        'primary' => 'assigned',
                        'info' => 'in_progress',
                        'success' => 'pending_review',
                        'danger' => 'rejected',
                        'success' => 'completed',
                        'gray' => 'archived',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending_assignment' => '待指派',
                        'assigned' => '已指派',
                        'in_progress' => '维修中',
                        'pending_review' => '待审核',
                        'rejected' => '已驳回',
                        'completed' => '已完成',
                        'archived' => '已归档',
                    })
                    ->label('状态'),

                TextColumn::make('creator.name')
                    ->label('创建人')
                    ->sortable(),

                TextColumn::make('assignedUser.name')
                    ->label('指派人')
                    ->sortable(),

                TextColumn::make('reviewer.name')
                    ->label('审核人')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('创建时间'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending_assignment' => '待指派',
                        'assigned' => '已指派',
                        'in_progress' => '维修中',
                        'pending_review' => '待审核',
                        'rejected' => '已驳回',
                        'completed' => '已完成',
                        'archived' => '已归档',
                    ])
                    ->label('状态'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('assign')
                    ->label('指派')
                    ->visible(fn(WorkOrder $record) => $record->status->getValue() === 'pending_assignment')
                    ->action(function (WorkOrder $record, array $data) {
                        $record->assignTo($data['assigned_user_id']);
                    })
                    ->form([
                        Select::make('assigned_user_id')
                            ->relationship('assignedUser', 'name')
                            ->required()
                            ->label('指派人'),
                    ]),
                Action::make('start_repair')
                    ->label('开始维修')
                    ->visible(fn(WorkOrder $record) => $record->status->getValue() === 'assigned')
                    ->action(function (WorkOrder $record) {
                        $record->startRepair();
                    }),
                Action::make('submit_review')
                    ->label('提交审核')
                    ->visible(fn(WorkOrder $record) => $record->status->getValue() === 'in_progress')
                    ->form([
                        Textarea::make('repair_details')
                            ->required()
                            ->label('维修详情'),
                    ])
                    ->action(function (WorkOrder $record, array $data) {
                        $record->submitForReview($data['repair_details']);
                    }),
                Action::make('approve')
                    ->label('通过')
                    ->visible(fn(WorkOrder $record) => $record->status->getValue() === 'pending_review')
                    ->action(function (WorkOrder $record) {
                        $record->approve();
                    }),
                Action::make('reject')
                    ->label('驳回')
                    ->visible(fn(WorkOrder $record) => $record->status->getValue() === 'pending_review')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->required()
                            ->label('驳回原因'),
                    ])
                    ->action(function (WorkOrder $record, array $data) {
                        $record->reject($data['rejection_reason']);
                    }),
                Action::make('archive')
                    ->label('归档')
                    ->visible(fn(WorkOrder $record) => $record->status->getValue() === 'completed')
                    ->action(function (WorkOrder $record) {
                        $record->archive();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
        ;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\WorkOrderHistoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkOrders::route('/'),
            'create' => Pages\CreateWorkOrder::route('/create'),
            'view' => Pages\ViewWorkOrder::route('/{record}'),
            'edit' => Pages\EditWorkOrder::route('/{record}/edit'),
        ];
    }
}
