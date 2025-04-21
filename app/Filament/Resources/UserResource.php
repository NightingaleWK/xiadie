<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;
use Tapp\FilamentAuditing\RelationManagers\AuditsRelationManager;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $modelLabel = '用户';
    protected static ?string $navigationGroup = '认证';
    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('users.name'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label(__('users.email'))
                    ->email()
                    ->required()
                    ->maxLength(255),

                Select::make('roles')
                    ->relationship('roles', 'nick_name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->optionsLimit(100)
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->nick_name ?? $record->name ?? 'Unknown'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),

                TextColumn::make('name')
                    ->label(__('users.name'))
                    ->searchable(),

                TextColumn::make('email')
                    ->label(__('users.email'))
                    ->searchable(),

                TextColumn::make('roles.nick_name')
                    ->label(__('users.roles'))
                    ->badge()
                    ->color(fn(string $state, $record): string => match ($record->roles->first()?->name ?? '') {
                        'admin', 'super_admin' => 'danger',
                        'creator' => 'warning',
                        'repairer' => 'info',
                        'reviewer' => 'success',
                        'archiver' => 'gray',
                        default => 'primary',
                    }),

                TextColumn::make('created_at')
                    ->label(__('users.created_at'))
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('users.updated_at'))
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('changePassword')
                    ->label(__('users.change_password'))
                    ->icon('heroicon-o-key')
                    ->form([
                        TextInput::make('password')
                            ->label(__('users.new_password'))
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->rule('confirmed'),

                        TextInput::make('password_confirmation')
                            ->label(__('users.confirm_password'))
                            ->password()
                            ->required(),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->update([
                            'password' => Hash::make($data['password']),
                        ]);
                    })
                    ->successNotification(
                        notification: fn(): Notification =>
                        Notification::make()
                            ->success()
                            ->title(__('users.password_updated'))
                            ->body(__('users.password_update_success')),
                    ),
                Tables\Actions\DeleteAction::make(),

                Impersonate::make()
                    ->icon('heroicon-o-lock-open')
                    ->color(fn() => Color::hex('#000000'))
                    ->link(),
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
            AuditsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }
}
