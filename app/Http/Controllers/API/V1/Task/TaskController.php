<?php

namespace App\Http\Controllers\API\V1\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Task\StoreRequest;
use App\Http\Requests\API\V1\Task\UpdateRequest;
use App\Http\Resources\API\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Cache::remember('tasks', 3600, function () {

            return Task::all();
        });

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created task.
     *
     * Creates a new task for the authenticated user using the validated
     * payload from the StoreRequest. Returns the created task as a
     * TaskResource.
     *
     * @unauthenticated false
     * @group Tasks
     *
     * @bodyParam title string required The title of the task.
     * @bodyParam description string The description of the task.
     * @bodyParam due_date date The due date for the task. Example: 2025-01-30
     *
     * @response 200 {
     *   "id": 1,
     *   "title": "My Task",
     *   "description": "Some details",
     *   "due_date": "2025-01-30",
     *   "created_at": "2025-01-15T12:00:00Z"
     * }
     */
    public function store(StoreRequest $request)
    {

        $data = $request->validated();

        $task = $request->user()->tasks()->create($data);

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return new TaskResource($task);

        //return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Task $task)
    {
        $task->update($request->validated());

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(null, 204);
    }

    public function search(Request $request)
    {
        $search = $request->query('search');

        $query = Task::query();

        if (!empty($search)) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(20);

        return TaskResource::collection($tasks);
    }
}

//
//$tasks = Cache::remember('tasks', 3600, function () {
//
//    return Task::all();
//});
//
//return TaskResource::collection($tasks);
