<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $projects = $request->user()->projects()->with('users')->paginate(15);
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
            'role'=>'admin',
            'joined_at'=>now()
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
        $project = $request->user()->projects()->with('users')->find($id);
        return $project;
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

        $project = $request->user()->projects()->with('users')->find($id);
        if(!$project){
            return response()->json([
                'message'=>'project not found'
            ],404);
        }else if($project['pivot']['role'] !== 'admin'){
            return response()->json([
                'message'=>'this action is unauthorized'
            ],401);
        }else{
            $project->update($validated);
            return $project;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $project = $request->user()->projects()->with('users')->find($id);
        if(!$project){
            return response()->json([
                'message'=>'project not found'
            ],404);
        }else if($project['pivot']['role'] !== 'admin'){
            return response()->json([
                'message'=>'this action is unauthorized'
            ],401);
        }else{
            $project->delete();
            return response()->json([
                'message'=>'project deleted successfully'
            ]);
        }
    }
}
