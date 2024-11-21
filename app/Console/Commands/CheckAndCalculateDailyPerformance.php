<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Department;
use App\Models\Activity;
use App\Models\UserActivity;
use App\Models\Performance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckAndCalculateDailyPerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activities:check-and-calculate-daily-performance {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica atividades não concluídas e calcula a porcentagem de conclusão diária.';

    /**
     * Execute the console command.
     */
    public function handle($date = null)
    {
        $today = $date ? Carbon::parse($date) : Carbon::today();
        $dayName  = $today->format('l'); // Nome do dia atual
        $todayFormatted = $today->format('Y-m-d');

        // Obter todos os departamentos
        $departments = Department::all();

        foreach ($departments as $department) {
            // Verifica se o departamento opera em escala
            if ($department->is_scale) {
                // Se for escala, verifica se hoje é um dia de folga
                $holidaySchedule = $department->holidays; // Pega as folgas do departamento

                // Garantir que seja um array
                $holidaySchedule = is_array($holidaySchedule) ? $holidaySchedule : json_decode($holidaySchedule, true);

                // Verifique se a data atual está nas folgas
                if (in_array($todayFormatted, $holidaySchedule)) {
                    continue; // Se for um dia de folga, pula para o próximo departamento
                }
            } else {
                // Se não for escala, verifica se o dia de hoje é um dia de trabalho normal (work_days)
                $workDays = $department->work_days;

                if (!in_array($dayName, $workDays)) {
                    continue; // Se não for um dia de trabalho normal, pula para o próximo departamento
                }
            }

            // Obter todas as atividades agendadas para hoje
            $activities = Activity::whereHas('departments', function($query) use ($department) {
                $query->where('departments.id', $department->id);
            })
            ->where('status', '!=', 0) // Ignorar atividades com status 0
            ->where(function($query) use ($today, $dayName) {
                $query->where('frequency', 'daily')
                      ->orWhere(function($query) use ($today) {
                          $query->where('frequency', 'specific_day')
                                ->whereJsonContains('specific_days', $today->format('l'));
                      });
            })
            ->get();

            $users = $department->members; // Obter os membros do departamento

            foreach ($users as $user) {
                $totalActivities = $activities->count();

                // Verifica se a atividade não foi concluída
                $uncompletedActivities = Activity::whereHas('departments', function($query) use ($department) {
                    $query->where('departments.id', $department->id);
                })
                ->where('status', '!=', 0) // Ignorar atividades com status 0
                ->whereNotIn('id', function($query) use ($user, $today) {
                    $query->select('activity_id')
                          ->from('user_activities')
                          ->where('user_id', $user->id)
                          ->whereDate('assigned_date', $today);
                })
                ->pluck('id');

                // Registrar atividades não concluídas
                foreach ($uncompletedActivities as $activityId) {
                    UserActivity::create([
                        'activity_id' => $activityId,
                        'user_id' => $user->id,
                        'status' => 'nao_concluido',
                        'assigned_date' => $today,
                    ]);

                    Log::info("Atividade não realizada marcada para o usuário {$user->id} na atividade {$activityId}");
                }

                // Agora calcula as atividades concluídas
                $completedActivities = UserActivity::where('user_id', $user->id)
                    ->whereIn('activity_id', $activities->pluck('id'))
                    ->where('status', 'concluido')
                    ->whereDate('assigned_date', $today)
                    ->count();

                if ($totalActivities > 0) {
                    $completionPercentage = ($completedActivities / $totalActivities) * 100;

                    Performance::create([
                        'user_id' => $user->id,
                        'date' => $today,
                        'completion_percentage' => $completionPercentage,
                    ]);

                    Log::info("Performance calculada para o usuário {$user->name} no departamento {$department->name}: {$completionPercentage}%");
                }
            }
        }

        Log::info('Verificação de atividades não concluídas e cálculo de performance concluídos.');
    }
}
