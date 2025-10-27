<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class ProjectCommentsController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project, string $id)
    {
        //
        $validated = $request->validate([
            'content'=>'required|string'
        ]);
        
        $currentUser = $request->user();
        if(!$project->users->contains($currentUser)){
            return response()->json([
                'message'=>'This action is unauthorized'
            ],401);
        }
        $task = $project->tasks()->find($id);
        if(!$task){
            return response()->json([
                'message'=>'task does not exist'
            ],404);
        }
        $currentRole = $project->users()
        ->where('user_id', $currentUser->id)
        ->first()
        ->pivot
        ->role ?? null;
        if (!roleCan($currentRole, 'add_comment')) {
            return response()->json(['message' => 'Not authorized to add comments.'], 401);
        }
        $validated['user_id'] = $request->user()->id;
        $comment = $task->comments()->create($validated);
        return response()->json([
            'status'=>'success',
            'message'=>'comment created successfully',
            'data'=> $comment
        ]);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Project $project, Comment $comment)
    {
        $validated = $request->validate([
            'content' => 'string'
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
        if (!roleCan($currentRole, 'edit_comment')) {
            return response()->json(['message' => 'Not authorized to edit comments.'], 401);
        }
        if(!$comment->user === $currentUser){
            return response()->json(['message' => 'Only authorized to edit your comments.'], 401);
            
        }
        $comment->update($validated);
        return response()->json([
            'status'=>'success',
            'message'=>'comment modified successfully',
            'data'=>$comment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Project $project, Comment $comment)
    {
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
        if (!roleCan($currentRole, 'edit_comment')) {
            return response()->json(['message' => 'Not authorized to delete comments.'], 401);
        }
        if(!$comment->user === $currentUser){
            return response()->json(['message' => 'Only authorized to delete your comments.'], 401);
            
        }

        $comment->delete();
        return response()->json([
            'message'=>'Comment deleted successfully'
        ]);
    }
}
