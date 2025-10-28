<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $projects = $request->user()->projects()->paginate(15);
        return $projects;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'name'=>'required|string|max:255'
        ]);
        $project = Project::create($validated);
        $project->users()->attach($request->user(),[
            'role'=>'owner'
        ]);
        return response()->json([
            'status'=>'success',
            'message'=>'project created successfully',
            'data'=>$project
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        //
        $project = $request->user()->projects()->find($id);
        if(!$project){
            return response()->json([
                'message' => 'project not found'
            ],404);
        }
        $tasks = $project->tasks()->paginate(15);
        return response()->json([
            'project'=>$project,
            'tasks'=>$tasks
        ]);
    }
    
    /**
     * Update the specified resource in storage.
    */
    public function update(Request $request, string $id)
    {
        //
        $validated = $request->validate([
            'name' => 'nullable|string|max:255'
        ]);
        
        $project = $request->user()->projects()->find($id);
        if(!$project){
            return response()->json([
                'message' => 'project not found'
            ],404);
        }
        $currentUser = $request->user();
        $currentRole = $project->users()
        ->where('user_id', $currentUser->id)
        ->first()
        ->pivot
        ->role ?? null;
        if(!$project){
            return response()->json([
                'message'=>'project not found'
            ],404);
        }if (!roleCan($currentRole, 'update_project')) {
            return response()->json(['message' => 'Not authorized to update project.'], 401);
        }
        $project->update($validated);
        return response()->json(['message' => 'Project updated successfully.']);
        
    }
    
    /**
     * Remove the specified resource from storage.
    */
    public function destroy(Request $request, string $id)
    {
        $project = $request->user()->projects()->find($id);
        if(!$project){
            return response()->json([
                'message' => 'project not found'
            ],404);
        }
        $currentUser = $request->user();
        $currentRole = $project->users()
            ->where('user_id', $currentUser->id)
            ->first()
            ->pivot
            ->role ?? null;
        if(!$project){
            return response()->json([
                'message'=>'project not found'
            ],404);
        }if (!roleCan($currentRole, 'delete_project')) {
            return response()->json(['message' => 'Not authorized to delete project.'], 401);
        }
        $project->delete();
        return response()->json(['message' => 'Project deleted successfully.']);
        
    }
}
