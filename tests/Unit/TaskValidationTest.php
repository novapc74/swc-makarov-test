<?php

namespace Tests\Unit;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class TaskValidationTest extends TestCase
{
    /**
     * Тест: StoreTaskRequest требует обязательные поля.
     */
    public function testStoreTaskRequestValidationFailsWithoutRequiredFields(): void
    {
        $request = new StoreTaskRequest();

        $validator = Validator::make([], $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    /**
     * Тест: StoreTaskRequest валидирует формат даты и статус.
     */
    public function testStoreTaskRequestValidationFailsWithInvalidData(): void
    {
        $request = new StoreTaskRequest();

        $data = [
            'title' => 'Test',
            'status' => 'invalid-status',
            'due_date' => '31-12-2024',
        ];

        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
        $this->assertArrayHasKey('due_date', $validator->errors()->toArray());
    }

    /**
     * Тест: UpdateTaskRequest позволяет пропускать поля (nullable/sometimes).
     */
    public function testUpdateTaskRequestValidationPassesWithPartialData(): void
    {
        $request = new UpdateTaskRequest();

        $data = ['status' => 'completed'];

        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    /**
     * Тест: Кастомные сообщения об ошибках в StoreTaskRequest.
     */
    public function testStoreTaskRequestHasCustomMessages(): void
    {
        $request = new StoreTaskRequest();
        $messages = $request->messages();

        $this->assertEquals('Заголовок задачи обязателен.', $messages['title.required']);
        $this->assertEquals('Дата обязательное поле в формате ГГГГ-ММ-ДД.', $messages['due_date.required']);

        $this->assertStringContainsString('pending, in_progress, completed', $messages['status.in']);
    }
}

