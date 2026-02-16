<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Mail\TaskCreatedMail;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Auth\Access\AuthorizationException;
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
        $task = $request->user()->tasks()->create($request->validated());

        Cache::tags('user_' . auth()->id())->flush();

        Mail::to($request->user())->queue(new TaskCreatedMail($task));

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
    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $this->authorize('update', $task);

        $data = $request->validated();

        if (isset($data['due_date']) && $data['due_date'] >= now()->toDateString()) {
            $data['is_overdue_notified'] = false;
        }

        $task->update($request->validated());

        Cache::tags('user_' . auth()->id())->flush();

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

