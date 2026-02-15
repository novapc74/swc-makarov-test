<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Mail\TaskOverdueMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckOverdueTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-overdue-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверка просроченных задач и уведомление пользователей';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $overdueTasks = Task::where('status', '!=', 'completed')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->toDateString())
            ->where('is_overdue_notified', false)
            ->with('user')
            ->get();

        foreach ($overdueTasks as $task) {
            Mail::to($task->user)->queue(new TaskOverdueMail($task));

            $task->is_overdue_notified = true;
            $task->save();
        }

        $this->info("Уведомления отправлены для {$overdueTasks->count()} задач.");
    }
}
