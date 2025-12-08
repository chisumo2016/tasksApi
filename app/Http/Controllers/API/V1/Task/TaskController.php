<?php

namespace App\Http\Controllers\API\V1\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Task\StoreRequest;
use App\Http\Requests\API\V1\Task\UpdateRequest;
use App\Http\Resources\API\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::paginate(1);

        return TaskResource::collection($tasks);

        //return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {

        $data = $request->validated();

        //dd($data);

        $task = $request->user()->tasks()->create($data);

        return new TaskResource($task);

       // return response()->json($task);
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
        $data = $request->validated();

        $task->update($data);

        return new TaskResource($task);
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
