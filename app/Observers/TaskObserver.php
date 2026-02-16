<?php

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Facades\Cache;

class TaskObserver
{
    private function clearCache(Task $task): void
    {
        Cache::tags(["user_{$task->user_id}_tasks"])->flush();
    }

    public function created(Task $task): void
    {
        $this->clearCache($task);
    }
    public function updated(Task $task): void
    {
        $this->clearCache($task);
    }
    public function deleted(Task $task): void
    {
        $this->clearCache($task);
    }
}
