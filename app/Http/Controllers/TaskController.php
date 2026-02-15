<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Mail\TaskCreatedMail;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreTaskRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    /**
     * GET /api/tasks
     */
    public function index(): AnonymousResourceCollection
    {
        $tasks = auth()->user()->tasks()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return TaskResource::collection($tasks);
    }

    /**
     * POST /api/tasks
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        // Создаем задачу через связь пользователя
        $task = $request->user()->tasks()->create($request->validated());

        // Отправляем письмо в очередь (Redis)
        Mail::to($request->user())->queue(new TaskCreatedMail($task));

        // Возвращаем JSON через Resource, статус 201 и заголовок Location
        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201)
            ->header('Location', route('tasks.show', $task->id));
    }

    /**
     * GET /api/tasks/{id}
     * @throws AuthorizationException
     */
    public function show(Task $task): TaskResource
    {
        $this->authorize('view', $task);

        return new TaskResource($task);
    }

    /**
     * PUT/PATCH /api/tasks/{id}
     * @throws AuthorizationException
     */
    public function update(StoreTaskRequest $request, Task $task): TaskResource
    {
        $this->authorize('update', $task);

        $task->update($request->validated());

        return new TaskResource($task);
    }

    /**
     * DELETE /api/tasks/{id}
     * @throws AuthorizationException
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json(null, 204);
    }

}

