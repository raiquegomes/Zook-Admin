<?php

namespace App\Filament\Collaborator\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

use Illuminate\Database\Eloquent\Builder;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;


class ApprovateWidget extends BaseWidget
{
    protected static ?string $heading = 'Atividades Pendentes para Aprovação';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user->departments->contains(fn($department) => $department->show_notice_board);
    }

    protected function getTableQuery(): Builder
    {
        $user = Auth::user();
        $userDepartmentIds = $user->departments->pluck('id');

            return UserActivity::query()
            ->where('status', 'em_analise') // Apenas atividades com status "em_analise"
            ->whereIn('department_master_id', $userDepartmentIds);
    }


    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('activity.title')
                    ->label('Título da Atividade')
                    ->sortable()
                    ->searchable()
                    ->extraAttributes(['class' => 'break-words'])
                    ->wrap(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuário')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('assigned_date')
                    ->label('Data')
                    ->date(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Aprovar')
                    ->color('success')
                    ->icon('heroicon-s-check')
                    ->action(function (UserActivity $record) {
                        $record->update(['status' => 'concluido']);
                        session()->flash('success', 'Atividade aprovada com sucesso!');
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Rejeitar')
                    ->color('danger')
                    ->action(function (UserActivity $record) {
                        $record->update(['status' => 'nao_aprovado']);
                        session()->flash('error', 'Atividade rejeitada.');
                    }),
                Tables\Actions\ViewAction::make('view')
                    ->label('Detalhes')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detalhes da Atividade')
                    ->record(function (UserActivity $record) {
                        return $record;
                    })
                    ->form([
                        Fieldset::make('Informações')
                        ->schema([
                            Placeholder::make('activity.title')
                            ->label('Título')
                            ->content(fn (UserActivity $record) => $record->activity->title ?? '-'),
                            Placeholder::make('user.name')
                            ->label('Usuário')
                            ->content(fn (UserActivity $record) => $record->user->name ?? '-'),
                            Placeholder::make('assigned_date')
                            ->label('Data')
                            ->content(fn (UserActivity $record) => $record->assigned_date ?? '-'),
                            Placeholder::make('status')
                            ->label('Status')
                            ->content(fn (UserActivity $record) => $record->status ?? '-'),
                        ])
                        ->columns(3),

                        Textarea::make('observation')
                        ->label('Observação'),

                        FileUpload::make('attachments')
                        ->directory('atividades')
                        ->label('Fotos do Cupom')
                        ->columnSpan('full')
                        ->multiple()
                        ->panelLayout('grid')
                        ->maxFiles(7)
                        ->openable()
                        ->visibility('public'),


                    ]),
            ]);
    }
}
