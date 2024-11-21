<?php

namespace App\Filament\Enterprise\Pages;

use Filament\Pages\Page;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Filament\Forms\Components\Section;

class AvaliationSupervisor extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Avaliação';
    protected static ?string $navigationGroup = 'Repositores';

    protected static string $view = 'filament.enterprise.pages.avaliation-supervisor';

    public $selectedUser = null;
    public $activitiesMade = [];
    public $activitiesNotMade = [];

    public function mount()
    {
        // Define o colaborador selecionado como nulo no início
        $this->selectedUser = null;
    }

    public function updatedSelectedUser($userId)
    {
        // Data de ontem
        $yesterday = Carbon::yesterday()->toDateString();

        // Atividades feitas pelo colaborador no dia anterior
        $this->activitiesMade = UserActivity::where('user_id', $userId)
            ->whereDate('assigned_date', $yesterday)
            ->where('status', 'concluido') // Ajuste o status de acordo com seu enum
            ->with('activity')
            ->get();

        // Atividades não feitas pelo colaborador no dia anterior
        $this->activitiesNotMade = Activity::whereNotIn('id', function ($query) use ($userId, $yesterday) {
                $query->select('activity_id')
                    ->from('user_activities')
                    ->where('user_id', $userId)
                    ->whereDate('assigned_date', $yesterday)
                    ->where('status', 'concluido'); // Ajuste o status aqui também
            })
            ->get();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
