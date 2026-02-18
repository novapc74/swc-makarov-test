<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TaskCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function testTasksIndexIsCachedAndInvalidated(): void
    {
        $user = User::factory()->create();
        Task::factory()->count(3)->create(['user_id' => $user->id]);

        $response1 = $this->actingAs($user)->getJson('/api/tasks');
        $response1->assertStatus(200);
        $response1->assertHeader('X-Cache', 'MISS');

        $response2 = $this->actingAs($user)->getJson('/api/tasks');
        $response2->assertStatus(200);
        $response2->assertHeader('X-Cache', 'HIT');

        $this->actingAs($user)->postJson('/api/tasks', [
            'title'       => 'New Task',
            'description' => 'Test description',
            'status'      => 'pending',
            'due_date'    => now()->addDay()->toDateString(),
        ])->assertStatus(201);

        $response3 = $this->actingAs($user)->getJson('/api/tasks');
        $response3->assertStatus(200);
        $response3->assertHeader('X-Cache', 'MISS');
    }

    public function testCacheInvalidatesOnTaskDeletion(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->getJson('/api/tasks');
        $this->actingAs($user)->getJson('/api/tasks')->assertHeader('X-Cache', 'HIT');

        $this->actingAs($user)->deleteJson("/api/tasks/{$task->id}")->assertStatus(204);

        $this->actingAs($user)->getJson('/api/tasks')->assertHeader('X-Cache', 'MISS');
    }
}
