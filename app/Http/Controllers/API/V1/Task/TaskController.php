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
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {

       $data = $request->validated();

        $task= $request->user()->tasks()->create($data);

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
}
