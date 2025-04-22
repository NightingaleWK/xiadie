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
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Support\Facades\Auth;
use App\States\PendingAssignment;
use App\States\Rejected;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Enums\FiltersLayout;
use App\Events\WorkOrderStatusChanged;

class WorkOrderResource extends Resource
{
    protected static ?string $model = WorkOrder::class;
    protected static ?string $modelLabel = '工单';
    protected static ?string $navigationGroup = '维修';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function form(Form $form): Form
    {
        $isCreatePage = request()->routeIs('filament.admin.resources.work-orders.create');

        return $form
            ->schema([
                Section::make(__('work-orders.sections.basic_info'))
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->label(__('work-orders.title'))
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->required()
                            ->columnSpanFull()
                            ->autosize()
                            ->label(__('work-orders.description'))
                            ->columnSpanFull(),

                        Select::make('project_id')
                            ->relationship('project', 'name')
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->optionsLimit(20)
                            ->label(__('work-orders.project_id')),

                        DateTimePicker::make('created_at')
                            ->label('工单创建时间')
                            ->disabled(),

                        DateTimePicker::make('updated_at')
                            ->label('工单最后更新时间')
                            ->disabled(),
                    ])->columns(3),

                Section::make(__('work-orders.sections.status_info'))
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending_assignment' => __('work-orders.statuses.pending_assignment'),
                                'assigned' => __('work-orders.statuses.assigned'),
                                'in_progress' => __('work-orders.statuses.in_progress'),
                                'pending_review' => __('work-orders.statuses.pending_review'),
                                'rejected' => __('work-orders.statuses.rejected'),
                                'completed' => __('work-orders.statuses.completed'),
                                'archived' => __('work-orders.statuses.archived'),
                            ])
                            ->native(false)
                            ->disabled()
                            ->dehydrated()
                            ->default('pending_assignment')
                            ->label(__('work-orders.status')),

                        Select::make('creator_user_id')
                            ->relationship('creator', 'name')
                            ->native(false)
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->default(function () {
                                return auth()->id();
                            })
                            ->label(__('work-orders.creator_user_id')),

                        Select::make('assigned_user_id')
                            ->native(false)
                            ->relationship('assignedUser', 'name')
                            ->disabled()
                            ->label(__('work-orders.assigned_user_id')),

                        Select::make('reviewer_user_id')
                            ->native(false)
                            ->relationship('reviewer', 'name')
                            ->disabled()
                            ->label(__('work-orders.reviewer_user_id')),
                    ])->columns(2),

                Section::make(__('work-orders.sections.repair_info'))
                    ->schema([
                        Textarea::make('repair_details')
                            ->columnSpanFull()
                            ->disabled()
                            ->autosize()
                            ->label(__('work-orders.repair_details')),

                        Textarea::make('rejection_reason')
                            ->columnSpanFull()
                            ->disabled()
                            ->autosize()
                            ->label(__('work-orders.rejection_reason')),

                        DateTimePicker::make('completed_at')
                            ->disabled()
                            ->columnSpan(1)
                            ->label(__('work-orders.completed_at')),

                        DateTimePicker::make('archived_at')
                            ->disabled()
                            ->columnSpan(1)
                            ->label(__('work-orders.archived_at')),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),

                TextColumn::make('title')
                    ->searchable()
                    ->wrap()
                    ->label(__('work-orders.title')),

                TextColumn::make('project.name')
                    ->placeholder('暂无关联项目')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('work-orders.project_id')),

                TextColumn::make('status')
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
                    ->formatStateUsing(fn(string $state): string => __("work-orders.statuses.{$state}"))
                    ->label(__('work-orders.status')),

                TextColumn::make('creator.name')
                    ->placeholder('暂无')
                    ->label(__('work-orders.creator_user_id'))
                    ->sortable(),

                TextColumn::make('assignedUser.name')
                    ->placeholder('暂无')
                    ->label(__('work-orders.assigned_user_id'))
                    ->sortable(),

                TextColumn::make('reviewer.name')
                    ->placeholder('暂无')
                    ->label(__('work-orders.reviewer_user_id'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('users.created_at'))
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('updated_at')
                    ->label(__('users.updated_at'))
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 标题关键字搜索
                Tables\Filters\Filter::make('title')
                    ->form([
                        Forms\Components\TextInput::make('title')
                            ->label('标题关键字')
                            ->placeholder('输入工单标题关键字')
                            ->columnSpan(2),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['title'],
                                fn(Builder $query, $title): Builder => $query->where('title', 'like', "%{$title}%")
                            );
                    }),

                // 项目筛选
                Tables\Filters\SelectFilter::make('project_id')
                    ->label(__('work-orders.project_id'))
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                // 创建人筛选
                Tables\Filters\SelectFilter::make('creator_user_id')
                    ->label(__('work-orders.creator_user_id'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                // 维修人筛选
                Tables\Filters\SelectFilter::make('assigned_user_id')
                    ->label(__('work-orders.assigned_user_id'))
                    ->relationship('assignedUser', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                // 审核人筛选
                Tables\Filters\SelectFilter::make('reviewer_user_id')
                    ->label(__('work-orders.reviewer_user_id'))
                    ->relationship('reviewer', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                // 创建日期范围筛选
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->displayFormat('Y-m-d')
                            ->label('创建日期从')
                            ->native(false),
                        Forms\Components\DatePicker::make('created_until')
                            ->displayFormat('Y-m-d')
                            ->label('创建日期至')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                // 更新日期范围筛选
                Tables\Filters\Filter::make('updated_at')
                    ->form([
                        Forms\Components\DatePicker::make('updated_from')
                            ->displayFormat('Y-m-d')
                            ->label('更新日期从')
                            ->native(false),
                        Forms\Components\DatePicker::make('updated_until')
                            ->displayFormat('Y-m-d')
                            ->label('更新日期至')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['updated_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('updated_at', '>=', $date),
                            )
                            ->when(
                                $data['updated_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('updated_at', '<=', $date),
                            );
                    }),

                // 完成日期范围筛选
                Tables\Filters\Filter::make('completed_at')
                    ->form([
                        Forms\Components\DatePicker::make('completed_from')
                            ->displayFormat('Y-m-d')
                            ->label('完成审核日期从')
                            ->native(false),
                        Forms\Components\DatePicker::make('completed_until')
                            ->displayFormat('Y-m-d')
                            ->label('完成审核日期至')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['completed_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('completed_at', '>=', $date),
                            )
                            ->when(
                                $data['completed_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('completed_at', '<=', $date),
                            );
                    }),

                // 归档日期范围筛选
                Tables\Filters\Filter::make('archived_at')
                    ->form([
                        Forms\Components\DatePicker::make('archived_from')
                            ->displayFormat('Y-m-d')
                            ->label('归档日期从')
                            ->native(false),
                        Forms\Components\DatePicker::make('archived_until')
                            ->label('归档日期至')
                            ->displayFormat('Y-m-d')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['archived_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('archived_at', '>=', $date),
                            )
                            ->when(
                                $data['archived_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('archived_at', '<=', $date),
                            );
                    }),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->actions([
                Action::make('assign')
                    ->label(__('work-orders.actions.assign'))
                    ->visible(fn(WorkOrder $record) => $record->status->getValue() === 'pending_assignment')
                    ->requiresConfirmation()
                    ->icon('heroicon-o-user-plus')
                    ->color('primary')
                    ->action(function (WorkOrder $record, array $data) {
                        $user = \App\Models\User::find($data['assigned_user_id']);
                        $record->assignTo($user, $data['notes']);
                    })
                    ->form([
                        Select::make('assigned_user_id')
                            ->relationship('assignedUser', 'name')
                            ->required()
                            ->native(false)
                            ->label(__('work-orders.assigned_user_id'))
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->name . ' - ' . ($record->roles->first()?->nick_name ?? $record->roles->first()?->name ?? '无角色')),

                        Textarea::make('notes')
                            ->label('注意事项')
                            ->default('请及时维修')
                            ->required(),
                    ]),

                Action::make('start_repair')
                    ->label(__('work-orders.actions.start_repair'))
                    ->visible(fn(WorkOrder $record) => $record->status->getValue() === 'assigned')
                    ->requiresConfirmation()
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->action(function (WorkOrder $record) {
                        $record->startRepair();
                    }),

                Action::make('refuse_assignment')
                    ->label(__('work-orders.actions.refuse_assignment'))
                    ->visible(fn(WorkOrder $record) => $record->status->getValue() === 'assigned')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('reason')
                            ->label('拒绝原因')
                            ->required()
                            ->placeholder('请说明拒绝接受任务的原因')
                    ])
                    ->action(function (WorkOrder $record, array $data) {
                        $record->refuseAssignment($data['reason']);
                    }),

                Action::make('submit_review')
                    ->label(__('work-orders.actions.submit_review'))
                    ->visible(fn(WorkOrder $record) => $record->status->getValue() === 'in_progress')
                    ->requiresConfirmation()
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('primary')
                    ->form([
                        Textarea::make('repair_details')
                            ->required()
                            ->label(__('work-orders.repair_details')),
                    ])
                    ->action(function (WorkOrder $record, array $data) {
                        $record->submitForReview($data['repair_details']);
                    }),

                Action::make('approve')
                    ->label(__('work-orders.actions.approve'))
                    ->visible(fn(WorkOrder $record) => $record->status->getValue() === 'pending_review')
                    ->requiresConfirmation()
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (WorkOrder $record) {
                        $record->reviewer_user_id = Auth::id();
                        $record->approve();
                    }),

                Action::make('reject')
                    ->label(__('work-orders.actions.reject'))
                    ->visible(fn(WorkOrder $record) => $record->status->getValue() === 'pending_review')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->required()
                            ->label(__('work-orders.rejection_reason')),
                    ])
                    ->action(function (WorkOrder $record, array $data) {
                        $record->reviewer_user_id = Auth::id();
                        $record->reject($data['rejection_reason']);
                    }),

                Action::make('restart_repair')
                    ->label(__('work-orders.actions.restart_repair'))
                    ->visible(fn(WorkOrder $record) => $record->status->getValue() === 'rejected')
                    ->requiresConfirmation()
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(function (WorkOrder $record) {
                        $record->restartRepair();
                    }),

                Action::make('archive')
                    ->label(__('work-orders.actions.archive'))
                    ->visible(fn(WorkOrder $record) => $record->status->getValue() === 'completed')
                    ->requiresConfirmation()
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->action(function (WorkOrder $record) {
                        $record->archive();
                    }),

                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->visible(function (WorkOrder $record) {
                            // 超级管理员可以随时编辑
                            if (Auth::user()->hasRole('super_admin')) {
                                return true;
                            }

                            // 创建者只能在pending_assignment状态下编辑
                            return Auth::id() === $record->creator_user_id &&
                                $record->status->getValue() === 'pending_assignment';
                        }),
                ]),
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
