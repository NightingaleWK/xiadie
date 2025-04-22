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
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Placeholder;

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
                Section::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                Section::make(__('work-orders.sections.basic_info'))
                                    ->schema([
                                        TextInput::make('title')
                                            ->required()
                                            ->maxLength(255)
                                            ->label(__('work-orders.title'))
                                            ->columnSpanFull()
                                            ->disabled(function ($record) {
                                                // 新建工单时不禁用
                                                if (!$record) return false;

                                                // 超级管理员可以编辑
                                                if (Auth::user()->hasRole('super_admin')) return false;

                                                // 非创建者无法编辑
                                                if (Auth::id() !== $record->creator_user_id) return true;

                                                // 创建者只能在待指派状态下编辑
                                                return $record->status->getValue() !== 'pending_assignment';
                                            }),

                                        Textarea::make('description')
                                            ->required()
                                            ->columnSpanFull()
                                            ->autosize()
                                            ->label(__('work-orders.description'))
                                            ->columnSpanFull()
                                            ->disabled(function ($record) {
                                                // 新建工单时不禁用
                                                if (!$record) return false;

                                                // 超级管理员可以编辑
                                                if (Auth::user()->hasRole('super_admin')) return false;

                                                // 非创建者无法编辑
                                                if (Auth::id() !== $record->creator_user_id) return true;

                                                // 创建者只能在待指派状态下编辑
                                                return $record->status->getValue() !== 'pending_assignment';
                                            }),

                                        Select::make('project_id')
                                            ->required()
                                            ->relationship('project', 'name')
                                            ->preload()
                                            ->searchable()
                                            ->native(false)
                                            ->optionsLimit(20)
                                            ->label(__('work-orders.project_id'))
                                            ->disabled(function ($record) {
                                                // 新建工单时不禁用
                                                if (!$record) return false;

                                                // 超级管理员可以编辑
                                                if (Auth::user()->hasRole('super_admin')) return false;

                                                // 非创建者无法编辑
                                                if (Auth::id() !== $record->creator_user_id) return true;

                                                // 创建者只能在待指派状态下编辑
                                                return $record->status->getValue() !== 'pending_assignment';
                                            }),

                                        DateTimePicker::make('created_at')
                                            ->label('工单创建时间')
                                            ->disabled(),

                                        DateTimePicker::make('updated_at')
                                            ->label('工单最后更新时间')
                                            ->disabled(),
                                    ])
                                    ->columns(1)
                                    ->columnSpan(3),

                                Section::make(__('work-orders.problem_attachments'))
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('problem_attachments')
                                            ->collection('problem_attachments')
                                            ->multiple()
                                            ->maxFiles(6)
                                            ->disk('public')
                                            ->visibility('public')
                                            ->downloadable()
                                            ->openable()
                                            ->label(__('work-orders.problem_attachments'))
                                            ->helperText('可上传工单相关的图片或文档（最多6个文件）')
                                            ->acceptedFileTypes(['image/*', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                            ->imagePreviewHeight('150')
                                            ->loadingIndicatorPosition('left')
                                            ->panelLayout('grid')
                                            ->panelAspectRatio('4:3')
                                            ->uploadProgressIndicatorPosition('center')
                                            ->imageResizeMode('contain')
                                            ->disabled(function ($record) {
                                                // 新建工单时不禁用
                                                if (!$record) return false;

                                                // 超级管理员可以编辑
                                                if (Auth::user()->hasRole('super_admin')) return false;

                                                // 非创建者无法编辑
                                                if (Auth::id() !== $record->creator_user_id) return true;

                                                // 创建者只能在待指派状态下编辑
                                                return $record->status->getValue() !== 'pending_assignment';
                                            }),
                                    ])
                                    ->columnSpan(2),
                            ])
                            ->columns(5),
                    ]),

                Section::make(__('work-orders.sections.status_info'))
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending_assignment' => __('work-orders.statuses.pending_assignment'),
                                'assigned'           => __('work-orders.statuses.assigned'),
                                'in_progress'        => __('work-orders.statuses.in_progress'),
                                'pending_review'     => __('work-orders.statuses.pending_review'),
                                'rejected'           => __('work-orders.statuses.rejected'),
                                'completed'          => __('work-orders.statuses.completed'),
                                'archived'           => __('work-orders.statuses.archived'),
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
                    ])->columns(4),

                Section::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                Section::make('故障记录')
                                    ->schema([
                                        CheckboxList::make('fault_types')
                                            ->options([
                                                'power'    => __('work-orders.fault_types_options.power'),
                                                'network'  => __('work-orders.fault_types_options.network'),
                                                'device'   => __('work-orders.fault_types_options.device'),
                                                'config'   => __('work-orders.fault_types_options.config'),
                                                'software' => __('work-orders.fault_types_options.software'),
                                                'wiring'   => __('work-orders.fault_types_options.wiring'),
                                            ])
                                            ->label(__('work-orders.fault_types'))
                                            // ->placeholder('暂无')
                                            ->disabled(function ($record) {
                                                // 超级管理员可以编辑
                                                if (Auth::user()->hasRole('super_admin')) return false;

                                                // 必须是维修人员
                                                if (!Auth::user()->hasRole('repairer')) return true;

                                                // 必须是指派给当前用户的工单
                                                if ($record && $record->assigned_user_id !== Auth::id()) return true;

                                                // 只有在维修中状态才能编辑
                                                return $record && $record->status->getValue() !== 'in_progress';
                                            })
                                            ->columns(3)
                                            ->gridDirection('row'),

                                        Textarea::make('repair_details')
                                            ->disabled(function ($record) {
                                                // 超级管理员可以编辑
                                                if (Auth::user()->hasRole('super_admin')) return false;

                                                // 必须是维修人员
                                                if (!Auth::user()->hasRole('repairer')) return true;

                                                // 必须是指派给当前用户的工单
                                                if ($record && $record->assigned_user_id !== Auth::id()) return true;

                                                // 只有在维修中状态才能编辑
                                                return $record && $record->status->getValue() !== 'in_progress';
                                            })
                                            ->autosize()
                                            ->label(__('work-orders.repair_details')),

                                        DateTimePicker::make('completed_at')
                                            ->disabled()
                                            ->label(__('work-orders.completed_at')),

                                        DateTimePicker::make('archived_at')
                                            ->disabled()
                                            ->label(__('work-orders.archived_at')),
                                    ])
                                    ->columnSpan(3),

                                Section::make(__('work-orders.repair_attachments'))
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('repair_attachments')
                                            ->collection('repair_attachments')
                                            ->multiple()
                                            ->maxFiles(6)
                                            ->disk('public')
                                            ->visibility('public')
                                            ->downloadable()
                                            ->openable()
                                            ->label(__('work-orders.repair_attachments'))
                                            ->helperText('可上传工单相关的图片或文档（最多6个文件）')
                                            ->acceptedFileTypes(['image/*', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                            ->imagePreviewHeight('150')
                                            ->loadingIndicatorPosition('left')
                                            ->panelLayout('grid')
                                            ->panelAspectRatio('4:3')
                                            ->uploadProgressIndicatorPosition('center')
                                            ->imageResizeMode('contain')
                                            ->disabled(function ($record) {
                                                // 超级管理员可以编辑
                                                if (Auth::user()->hasRole('super_admin')) return false;

                                                // 非维修人员无法编辑
                                                if (!Auth::user()->hasRole('repairer')) return true;

                                                // 必须是指派给当前用户的工单
                                                if ($record && $record->assigned_user_id !== Auth::id()) return true;

                                                // 只有在维修中状态才能编辑
                                                return $record && $record->status->getValue() !== 'in_progress';
                                            }),
                                    ])->columnSpan(2),
                            ])
                            ->columns(5),
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
                        'assigned'           => 'purple',
                        'in_progress'        => 'orange',
                        'pending_review'     => 'indigo',
                        'rejected'           => 'danger',
                        'completed'          => 'success',
                        'archived'           => 'slate',
                        default              => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => __("work-orders.statuses.{$state}"))
                    ->label(__('work-orders.status')),

                TextColumn::make('fault_types')
                    ->label(__('work-orders.fault_types'))
                    ->badge()
                    ->placeholder('暂无')
                    ->getStateUsing(function ($record) {
                        if (empty($record->fault_types)) return null;

                        $types = is_array($record->fault_types) ? $record->fault_types : json_decode($record->fault_types, true);

                        if (empty($types)) return null;

                        $labels = [
                            'power'    => __('work-orders.fault_types_options.power'),
                            'network'  => __('work-orders.fault_types_options.network'),
                            'device'   => __('work-orders.fault_types_options.device'),
                            'config'   => __('work-orders.fault_types_options.config'),
                            'software' => __('work-orders.fault_types_options.software'),
                            'wiring'   => __('work-orders.fault_types_options.wiring'),
                        ];

                        return collect($types)->map(function ($type) use ($labels) {
                            return $labels[$type] ?? $type;
                        });
                    })
                    ->listWithLineBreaks(),

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

                TextColumn::make('attachments_count')
                    ->label('附件数量')
                    ->getStateUsing(function ($record) {
                        $problemCount = $record->getMedia('problem_attachments')->count();
                        $repairCount = $record->getMedia('repair_attachments')->count();

                        if ($problemCount + $repairCount == 0) {
                            return null;
                        }

                        return $problemCount + $repairCount;
                    })
                    ->description(function ($record) {
                        $problemCount = $record->getMedia('problem_attachments')->count();
                        $repairCount = $record->getMedia('repair_attachments')->count();

                        if ($problemCount + $repairCount == 0) {
                            return null;
                        }

                        $parts = [];
                        if ($problemCount > 0) {
                            $parts[] = "问题：{$problemCount}";
                        }
                        if ($repairCount > 0) {
                            $parts[] = "维修：{$repairCount}";
                        }

                        return implode(' | ', $parts);
                    })
                    ->icon('heroicon-o-paper-clip')
                    ->iconPosition('before'),

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

                // 故障类型筛选
                Tables\Filters\Filter::make('fault_types')
                    ->form([
                        CheckboxList::make('fault_types')
                            ->options([
                                'power' => __('work-orders.fault_types_options.power'),
                                'network' => __('work-orders.fault_types_options.network'),
                                'device' => __('work-orders.fault_types_options.device'),
                                'config' => __('work-orders.fault_types_options.config'),
                                'software' => __('work-orders.fault_types_options.software'),
                                'wiring' => __('work-orders.fault_types_options.wiring'),
                            ])
                            ->label(__('work-orders.fault_types'))
                            ->columnSpan(2),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['fault_types'],
                            function (Builder $query, $types) {
                                foreach ($types as $type) {
                                    $query->where('fault_types', 'like', "%$type%");
                                }
                                return $query;
                            }
                        );
                    }),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->actions([
                Action::make('assign')
                    ->label(__('work-orders.actions.assign'))
                    ->visible(function (WorkOrder $record) {
                        // 只允许工单创建者进行指派，且工单状态为待指派
                        return Auth::user()->hasRole('creator') &&
                            $record->status->getValue() === 'pending_assignment';
                    })
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
                    ->visible(function (WorkOrder $record) {
                        // 只允许维修人员开始维修，且工单状态为已指派
                        return Auth::user()->hasRole('repairer') &&
                            $record->status->getValue() === 'assigned' &&
                            $record->assigned_user_id === Auth::id();
                    })
                    ->requiresConfirmation()
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->action(function (WorkOrder $record) {
                        $record->startRepair();
                    }),

                Action::make('refuse_assignment')
                    ->label(__('work-orders.actions.refuse_assignment'))
                    ->visible(function (WorkOrder $record) {
                        // 只允许维修人员拒绝指派，且工单状态为已指派
                        return Auth::user()->hasRole('repairer') &&
                            $record->status->getValue() === 'assigned' &&
                            $record->assigned_user_id === Auth::id();
                    })
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
                    ->visible(function (WorkOrder $record) {
                        // 只允许维修人员提交审核，且工单状态为维修中
                        return Auth::user()->hasRole('repairer') &&
                            $record->status->getValue() === 'in_progress' &&
                            $record->assigned_user_id === Auth::id();
                    })
                    ->requiresConfirmation()
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('primary')
                    ->form([
                        Textarea::make('repair_details')
                            ->required()
                            ->label(__('work-orders.repair_details')),

                        CheckboxList::make('fault_types')
                            ->options([
                                'power'    => __('work-orders.fault_types_options.power'),
                                'network'  => __('work-orders.fault_types_options.network'),
                                'device'   => __('work-orders.fault_types_options.device'),
                                'config'   => __('work-orders.fault_types_options.config'),
                                'software' => __('work-orders.fault_types_options.software'),
                                'wiring'   => __('work-orders.fault_types_options.wiring'),
                            ])
                            ->required()
                            ->label(__('work-orders.fault_types'))
                            ->helperText('请选择适用的故障类型，可多选'),

                        SpatieMediaLibraryFileUpload::make('repair_attachments')
                            ->collection('repair_attachments')
                            ->multiple()
                            ->maxFiles(6)
                            ->disk('public')
                            ->visibility('public')
                            ->downloadable()
                            ->openable()
                            ->label(__('work-orders.repair_attachments'))
                            ->helperText('可上传维修证明照片或文档（最多6个文件）')
                            ->acceptedFileTypes(['image/*', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->imagePreviewHeight('150')
                            ->loadingIndicatorPosition('left')
                            ->panelLayout('grid')
                            ->panelAspectRatio('4:3')
                            ->uploadProgressIndicatorPosition('center')
                            ->imageResizeMode('contain')
                            ->required(),
                    ])
                    ->action(function (WorkOrder $record, array $data) {
                        $record->fault_types = $data['fault_types'];
                        $record->submitForReview($data['repair_details']);
                    }),

                Action::make('approve')
                    ->label(__('work-orders.actions.approve'))
                    ->visible(function (WorkOrder $record) {
                        // 只允许审核人员通过审核，且工单状态为待审核
                        return Auth::user()->hasRole('reviewer') &&
                            $record->status->getValue() === 'pending_review';
                    })
                    ->requiresConfirmation()
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Textarea::make('comment')
                            ->label('审核意见')
                            ->placeholder('请输入审核通过意见（选填）')
                    ])
                    ->action(function (WorkOrder $record, array $data) {
                        $record->reviewer_user_id = Auth::id();
                        $record->approve($data['comment'] ?? null);
                    }),

                Action::make('reject')
                    ->label(__('work-orders.actions.reject'))
                    ->visible(function (WorkOrder $record) {
                        // 只允许审核人员驳回，且工单状态为待审核
                        return Auth::user()->hasRole('reviewer') &&
                            $record->status->getValue() === 'pending_review';
                    })
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
                    ->visible(function (WorkOrder $record) {
                        // 只允许维修人员重新维修，且工单状态为已驳回
                        return Auth::user()->hasRole('repairer') &&
                            $record->status->getValue() === 'rejected' &&
                            $record->assigned_user_id === Auth::id();
                    })
                    ->requiresConfirmation()
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(function (WorkOrder $record) {
                        $record->restartRepair();
                    }),

                Action::make('archive')
                    ->label(__('work-orders.actions.archive'))
                    ->visible(function (WorkOrder $record) {
                        // 只允许归档人员归档，且工单状态为已完成
                        return Auth::user()->hasRole('archiver') &&
                            $record->status->getValue() === 'completed';
                    })
                    ->requiresConfirmation()
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->form([
                        Textarea::make('comment')
                            ->label('归档意见')
                            ->placeholder('请输入归档意见（选填）')
                    ])
                    ->action(function (WorkOrder $record, array $data) {
                        $record->archive($data['comment'] ?? null);
                    }),

                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->visible(function (WorkOrder $record) {
                            // 所有角色都可以查看
                            return true;
                        }),
                    Tables\Actions\EditAction::make()
                        ->visible(function (WorkOrder $record) {
                            // 只有超级管理员和工单创建者可以编辑，且创建者只能在待指派状态下编辑
                            if (Auth::user()->hasRole('super_admin')) {
                                return true;
                            }

                            return Auth::user()->hasRole('creator') &&
                                Auth::id() === $record->creator_user_id &&
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
            RelationManagers\WorkOrderHistoriesRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWorkOrders::route('/'),
            'create' => Pages\CreateWorkOrder::route('/create'),
            'view'   => Pages\ViewWorkOrder::route('/{record}'),
            'edit'   => Pages\EditWorkOrder::route('/{record}/edit'),
        ];
    }
}
