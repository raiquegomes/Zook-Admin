<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Department;
use App\Models\Activity;
use App\Models\UserActivity;

class GenerateUserActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-user-activities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily activities for users based on their department schedules.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $departments = Department::with('users')->get();

        foreach ($departments as $department) {
            $workDays = json_decode($department->work_days, true);
            $today = now()->format('l'); // e.g., 'Monday'

            if (in_array($today, $workDays)) {
                $activities = Activity::where('department_id', $department->id)
                    ->where(function($query) use ($today) {
                        $query->where('frequency', 'daily')
                              ->orWhere(function($query) use ($today) {
                                  $query->where('frequency', 'specific_day')
                                        ->whereJsonContains('specific_days', $today);
                              });
                              // Adicionar condições para outras frequências como '5th_working_day', 'weekly', 'monthly' se necessário
                    })
                    ->get();

                foreach ($activities as $activity) {
                    if ($this->shouldGenerateActivity($activity)) {
                        foreach ($department->users as $user) {
                            UserActivity::create([
                                'user_id' => $user->id,
                                'activity_id' => $activity->id,
                                'assigned_date' => now(),
                            ]);
                        }
                    }
                }
            }
        }
    }

    private function shouldGenerateActivity(Activity $activity)
    {
        switch ($activity->frequency) {
            case 'daily':
                return true;
            case 'specific_day':
                return in_array(now()->format('l'), json_decode($activity->specific_days, true));
            case '5th_working_day':
                // Implementar lógica para verificar se hoje é o 5° dia útil do mês
            case 'weekly':
                // Implementar lógica semanal
            case 'monthly':
                // Implementar lógica mensal
        }

        return false;
    }
}
