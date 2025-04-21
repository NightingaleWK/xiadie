<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationResource\Pages;
use App\Filament\Resources\OrganizationResource\RelationManagers;
use App\Models\Organization;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = '组织结构';
    protected static ?string $navigationGroup = '系统设置';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('organizations.sections.basic_info'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('organizations.name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('code')
                            ->label(__('organizations.code'))
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Textarea::make('description')
                            ->label(__('organizations.description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('organizations.sections.hierarchy_info'))
                    ->schema([
                        SelectTree::make('parent_id')
                            ->label(__('organizations.parent_id'))
                            ->required()
                            ->relationship('parent', 'name', 'parent_id')  // 使用树建立 BelongsTo 关系
                            ->searchable()  // 激活搜索功能
                            ->enableBranchNode()  // 启用组选择
                            ->withCount(),  // 在群组名称旁边显示下级数量
                    ]),

                Section::make(__('organizations.sections.status_info'))
                    ->schema([
                        Toggle::make('is_active')
                            ->label(__('organizations.is_active'))
                            ->required()
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('organizations.name'))
                    ->searchable(),

                TextColumn::make('code')
                    ->label(__('organizations.code'))
                    ->searchable(),

                TextColumn::make('hierarchy_path')
                    ->label(__('organizations.hierarchy_path'))
                    ->description(fn(Organization $record) => $record->parent ? '上级：' . $record->parent->name : null)
                    ->getStateUsing(fn(Organization $record): string => $record->getFullHierarchyPath(' / '))
                    ->searchable(false)
                    ->wrap(),

                TextColumn::make('level')
                    ->label(__('organizations.level'))
                    ->numeric()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('organizations.is_active'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(__('organizations.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('parent_id_tree')
                    ->form([
                        SelectTree::make('parent_id')->label(trans('organizations.parent_id'))
                            ->relationship('parent', 'name', 'parent_id')
                            ->independent(false)
                            ->enableBranchNode()
                            ->searchable()
                            ->withCount()
                            ->placeholder('全部'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['parent_id'])) {
                            // 直接筛选父级ID等于所选组织ID的记录（只筛选直接子组织）
                            return $query->where('parent_id', $data['parent_id']);
                        }

                        return $query; // 默认情况下不进行过滤
                    }),
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
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'view' => Pages\ViewOrganization::route('/{record}'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}
