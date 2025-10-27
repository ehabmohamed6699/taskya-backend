<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Dom\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
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
    public function store(Request $request, Task $task)
    {
        //
        $validated = $request->validate([
            'content'=>'required|string'
        ]);
        if(!$task){
            return response()->json([
                'message'=>'task does not exist'
            ],404);
        }
        if($task->user() !== $request->user()){
            return response()->json([
                'message'=>'this action is unauthorized'
            ],403);
        }
        $validated['user_id'] = $request->user()->id;
        $comment = $task->comments()->create($validated);
        return response()->json([
            'status'=>'success',
            'message'=>'Comment created successfully',
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
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'content' => 'string'
        ]);
        //
        $comment = $request->user()->comments()->find($id);
        if(!$comment){
            return response()->json([
                'message'=>'comment not found'
            ],404);
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
    public function destroy(Request $request, string $id)
    {
        //
        $comment = $request->user()->comments()->find($id);
        if(!$comment){
            return response()->json([
                'message'=>'comment not found'
            ],404);
        }
        $comment->delete();
        return response()->json([
            'message'=>'Comment deleted successfully'
        ]);
    }
}
