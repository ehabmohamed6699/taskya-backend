<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tasks = $request->user()->tasks()->whereNull('project_id');
        //
        return response()->json([
            'data' => $tasks->paginate(15)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'string',
            'priority' => 'required|integer|in:0,1,2',
            'due_date' => 'required|date|after_or_equal:today'
        ]);
        $task = $request->user()->tasks()->create($validated);
        return response()->json([
            'status' => 'success',
            'message' => 'task created successfully',
            'data' => $task
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        //
        $task = $request->user()->tasks()->whereNull('project_id')->find($id);
        if (!$task) {
            return response()->json([
                'message' => 'Task not found'
            ], 404);
        }
        $comments = $task->comments()->paginate(15);
        return response()->json([
            'task' => $task,
            'comments' => $comments
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'priority' => 'integer|in:0,1,2',
            'due_date' => 'date|after_or_equal:today'
        ]);
        //
        $task = $request->user()->tasks()->whereNull('project_id')->find($id);
        if (!$task) {
            return response()->json([
                'message' => 'Task not found'
            ], 404);
        }
        $task->update($validated);
        return response()->json([
            'status' => 'success',
            'message' => 'task modified successfully',
            'data' => $task
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $task = $request->user()->tasks()->whereNull('project_id')->find($id);
        if (!$task) {
            return response()->json([
                'message' => 'Task not found'
            ], 404);
        }
        $task->delete();
        return response()->json([
            'message' => 'Task deleted successfully'
        ]);

        //
    }

    public function toggleCompleted(Request $request, Task $task)
    {
        $task['completed'] = !$task['completed'];
        $task->save();
        return $task;
    }
}
