<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class ProjectTasksController extends Controller
{
    //
    public function getTasks(Request $request, Project $project){
        $currentUser = $request->user();
        if(!$project->users->contains($currentUser)){
            return response()->json([
                'message'=>'This action is unauthorized'
            ],401);
        }
        return response()->json($project->tasks);
    }
    public function addTask(Request $request, Project $project){
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'string',
            'priority' => 'required|integer|in:0,1,2',
            'due_date' => 'required|date|after_or_equal:today'
        ]);
        $currentUser = $request->user();
        if(!$project->users->contains($currentUser)){
            return response()->json([
                'message'=>'This action is unauthorized'
            ],401);
        }
        $currentRole = $project->users()
            ->where('user_id', $currentUser->id)
            ->first()
            ->pivot
            ->role ?? null;
        if (!roleCan($currentRole, 'create_task')) {
            return response()->json(['message' => 'Not authorized to add tasks.'], 401);
        }
        $task = $project->tasks()->create([
            ...$validated,
            'user_id' => $currentUser->id,
        ]);
        return response()->json([
            'status'=>'success',
            'message'=>'task created successfully',
            'data'=>$task
        ]);
    }

    public function showTask(Request $request, Project $project, string $id){
        $currentUser = $request->user();
        if(!$project->users->contains($currentUser)){
            return response()->json([
                'message'=>'This action is unauthorized'
            ],401);
        }
        $task = $project->tasks()->find($id);
        if(!$task){
            return response()->json([
                'message'=>'Task not found'
            ],404);
        }
        $comments = $task->comments()->paginate(15);
        return response()->json([
            'task'=>$task,
            'comments'=>$comments
        ]);
    }
    public function editTask(Request $request, Project $project, string $id){
        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'priority' => 'integer|in:0,1,2',
            'due_date' => 'date|after_or_equal:today'
        ]);
        //
        
        $currentUser = $request->user();
        if(!$project->users->contains($currentUser)){
            return response()->json([
                'message'=>'This action is unauthorized'
            ],401);
        }
        $currentRole = $project->users()
        ->where('user_id', $currentUser->id)
        ->first()
        ->pivot
        ->role ?? null;
        if (!roleCan($currentRole, 'edit_task')) {
            return response()->json(['message' => 'Not authorized to edit tasks.'], 401);
        }
        $task = $project->tasks()->find($id);
        if(!$task){
            return response()->json([
                'message'=>'Task not found'
            ],404);
        }
        if($task->user !== $currentUser){
            return response()->json(['message' => 'Only authorized to edit your tasks.'], 401);
            
        }
        $task->update($validated);
        return response()->json([
            'status'=>'success',
            'message'=>'task modified successfully',
            'data'=>$task
        ]);
    }
    public function deleteTask(Request $request, Project $project, string $id){
        //
        $currentUser = $request->user();
        if(!$project->users->contains($currentUser)){
            return response()->json([
                'message'=>'This action is unauthorized'
            ],401);
        }
        $currentRole = $project->users()
        ->where('user_id', $currentUser->id)
        ->first()
        ->pivot
        ->role ?? null;
        if (!roleCan($currentRole, 'delete_task')) {
            return response()->json(['message' => 'Not authorized to delete tasks.'], 401);
        }
        $task = $project->tasks()->find($id);
        if(!$task){
            return response()->json([
                'message'=>'Task not found'
            ],404);
        }
        if($task->user !== $currentUser){
            return response()->json(['message' => 'Only authorized to delete your tasks.'], 401);
            
        }
        $task->delete();
        return response()->json([
            'message'=>'Task deleted successfully'
        ]);
    }
}