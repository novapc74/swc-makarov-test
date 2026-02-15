<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Mail\TaskOverdueMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckOverdueTasksTest extends TestCase
{
    use RefreshDatabase;

    public function testCheckOverdueTasksCommandSendsEmails()
    {
        Mail::fake();
        $user = User::factory()->create();

        Task::factory()->create([
            'user_id' => $user->id,
            'due_date' => now()->subDay()->toDateString(),
            'is_overdue_notified' => true,
        ]);

        $this->artisan('app:check-overdue-tasks');

        Mail::assertNotQueued(TaskOverdueMail::class);
    }
}
