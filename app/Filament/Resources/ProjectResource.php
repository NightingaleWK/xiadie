<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = '项目管理';
    protected static ?string $navigationGroup = '系统设置';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('projects.sections.basic_info'))
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('projects.name'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        TextInput::make('name_en')
                            ->label(__('projects.name_en'))
                            ->maxLength(255)
                            ->columnSpan(1),

                        TextInput::make('code')
                            ->label(__('projects.code'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Textarea::make('description')
                            ->label(__('projects.description'))
                            ->columnSpan(3),
                    ]),

                Section::make(__('projects.sections.date_info'))
                    ->columns(3)
                    ->schema([
                        DatePicker::make('start_date')
                            ->label(__('projects.start_date'))
                            ->format('Y-m-d')
                            ->native(false)
                            ->columnSpan(1),

                        DatePicker::make('operation_date')
                            ->label(__('projects.operation_date'))
                            ->format('Y-m-d')
                            ->native(false)
                            ->columnSpan(1),

                        DatePicker::make('end_date')
                            ->label(__('projects.end_date'))
                            ->format('Y-m-d')
                            ->native(false)
                            ->columnSpan(1),
                    ]),

                Section::make(__('projects.sections.contact_info'))
                    ->columns(3)
                    ->schema([
                        TextInput::make('project_manager')
                            ->label(__('projects.project_manager'))
                            ->maxLength(255)
                            ->columnSpan(1),

                        TextInput::make('manager_phone')
                            ->label(__('projects.manager_phone'))
                            ->tel()
                            ->maxLength(255)
                            ->columnSpan(1),
                    ]),

                Section::make(__('projects.sections.client_info'))
                    ->columns(3)
                    ->schema([
                        TextInput::make('client_name')
                            ->label(__('projects.client_name'))
                            ->maxLength(255)
                            ->columnSpan(1),

                        TextInput::make('client_contact')
                            ->label(__('projects.client_contact'))
                            ->maxLength(255)
                            ->columnSpan(1),

                        TextInput::make('client_phone')
                            ->label(__('projects.client_phone'))
                            ->tel()
                            ->maxLength(255)
                            ->columnSpan(1),
                    ]),

                Section::make(__('projects.sections.status_info'))
                    ->columns(3)
                    ->schema([
                        Radio::make('status')
                            ->label(__('projects.status'))
                            ->required()
                            ->options(Project::getStatusOptions())
                            ->default('planning')
                            ->inline()
                            ->columnSpanFull(),

                        Textarea::make('remarks')
                            ->label(__('projects.remarks'))
                            ->columnSpan(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('projects.name'))
                    ->description(fn(Project $record): string => $record->name_en)
                    ->searchable(),

                TextColumn::make('code')
                    ->label(__('projects.code'))
                    ->searchable(),

                TextColumn::make('start_date')
                    ->label(__('projects.start_date'))
                    ->placeholder('暂无')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('operation_date')
                    ->label(__('projects.operation_date'))
                    ->placeholder('暂无')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label(__('projects.end_date'))
                    ->placeholder('暂无')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('project_manager')
                    ->label(__('projects.project_manager'))
                    ->description(fn(Project $record): string => $record->manager_phone)
                    ->searchable(),

                TextColumn::make('client_name')
                    ->label(__('projects.client_name'))
                    ->searchable(),

                TextColumn::make('client_contact')
                    ->label(__('projects.client_contact'))
                    ->description(fn(Project $record): string => $record->client_phone)
                    ->searchable(),

                TextColumn::make('status')
                    ->label(__('projects.status'))
                    ->formatStateUsing(fn(string $state): string => Project::getStatusOptions()[$state] ?? $state)
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label(__('projects.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('projects.updated_at'))
                    ->dateTime()
                    ->sortable()
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
