<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Mail\TaskCreatedMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест: Авторизованный пользователь видит только СВОИ задачи.
     */
    public function testUserCanListOnlyTheirTasks(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        Task::factory()->create(['user_id' => $user->id, 'title' => 'My Task']);
        Task::factory()->create(['user_id' => $anotherUser->id, 'title' => 'Alien Task']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'My Task');
    }

    /**
     * Тест: Создание задачи, проверка заголовка Location и очереди писем.
     */
    public function testUserCanCreateTaskAndMailIsQueued(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $taskData = [
            'title' => 'New Test Task',
            'description' => 'Test description',
            'status' => 'pending',
            'due_date' => '2026-12-31',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertHeader('Location');

        $this->assertDatabaseHas('tasks', [
            'title' => 'New Test Task',
            'user_id' => $user->id
        ]);

        Mail::assertQueued(TaskCreatedMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /**
     * Тест: Нельзя просматривать/удалять чужую задачу (403).
     */
    public function testUserCannotAccessOthersTask(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $anotherUser->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }
}

