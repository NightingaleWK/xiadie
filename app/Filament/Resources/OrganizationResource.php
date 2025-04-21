<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationResource\Pages;
use App\Filament\Resources\OrganizationResource\RelationManagers;
use App\Models\Organization;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = '组织管理';

    protected static ?string $modelLabel = '组织';

    protected static ?string $pluralModelLabel = '组织';

    protected static ?string $navigationGroup = '组织结构';

    public static function getModelLabel(): string
    {
        return __('organizations.model.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('organizations.model.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('organizations.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('organizations.navigation_group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('organizations.sections.basic_info'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('organizations.name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label(__('organizations.code'))
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('description')
                            ->label(__('organizations.description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('organizations.sections.hierarchy_info'))
                    ->schema([
                        Forms\Components\Select::make('parent_id')
                            ->label(__('organizations.parent_id'))
                            ->relationship('parent', 'name'),
                    ]),

                Section::make(__('organizations.sections.status_info'))
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
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
                Tables\Columns\TextColumn::make('name')
                    ->label(__('organizations.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label(__('organizations.code'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label(__('organizations.parent_id')),
                Tables\Columns\TextColumn::make('level')
                    ->label(__('organizations.level'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('organizations.is_active'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('organizations.created_at'))
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
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'view' => Pages\ViewOrganization::route('/{record}'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}
