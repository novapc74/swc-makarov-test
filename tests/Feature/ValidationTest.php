<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест кастомного формата ошибки для StoreTaskRequest
     */
    public function testStoreTaskValidationCustomFormat()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/tasks', [
                'status' => 'invalid_status', // Ошибка здесь
                'title' => '',                // И здесь
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Ошибка валидации',
                'code' => 422,
            ])
            ->assertJsonValidationErrors(['status', 'title', 'due_date']);

        $response->assertJsonPath('errors.status.0', 'Выбранный статус недействителен. Допустимые значения: pending, in_progress, completed.');
    }

    /**
     * Тест валидации регистрации (StoreUserRequest)
     */
    public function testRegistrationValidation()
    {
        $response = $this->postJson('/api/register', [
            'email' => 'not-an-email',
            'password' => '123', // Короткий и без confirmation
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * Тест гибкой валидации при обновлении (UpdateTaskRequest)
     */
    public function testUpdateTasAllowsPartialData()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->patchJson("/api/tasks/{$task->id}", [
                'status' => 'completed'
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed'
        ]);
    }

    /**
     * Тест логина (AuthUserRequest)
     */
    public function testLoginValidationRequiresFields()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }
}

