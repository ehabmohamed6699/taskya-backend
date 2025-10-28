<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;

use Illuminate\Http\Request;

class ProjectMemberController extends Controller
{
    public function listMembers(Request $request, Project $project)
    {
        if(!$request->user()->projects->contains($project)){
            return response()->json([
                'message' => 'project not found'
            ],404);
        }
        $members = $project->users()
            ->select('users.id', 'users.name', 'users.email', 'project_user.role')
            ->get();

        return response()->json($members);
    }

    public function addMember(Request $request, Project $project){
        $request->validate([
            'email'=>'required|email|exists:users,email',
            'role'=>'required|in:manager,member,viewer'
        ]);
        $currentUser = $request->user();
        $currentRole = $project->users()
            ->where('user_id', $currentUser->id)
            ->first()
            ->pivot
            ->role ?? null;
        if (!roleCan($currentRole, 'add_member')) {
            return response()->json(['message' => 'Not authorized to add members.', 'role'=>$currentRole], 401);
        }
        $newUser = User::where('email', $request->email)->first();

        if ($project->users()->where('user_id', $newUser->id)->exists()) {
            return response()->json(['message' => 'User already in project.'], 400);
        }else if(!$newUser){
            return response()->json(['message' => 'User not found.'], 404);

        }

        $project->users()->attach($newUser->id, ['role' => $request->role]);

        return response()->json(['message' => 'Member added successfully.']);
    }
    public function changeMemberRole(Request $request, Project $project, User $user)
    {
        $request->validate([
            'role'=>'required|in:manager,member,viewer'
        ]);
        $currentUser = $request->user();
        $currentRole = $project->users()
            ->where('user_id', $currentUser->id)
            ->first()
            ->pivot
            ->role ?? null;

        if (!roleCan($currentRole, 'change_role')) {
            return response()->json(['message' => 'Not authorized to change roles.'], 401);
        }

        $targetMember = $project->users()->where('user_id', $user->id)->first();

        if (!$targetMember) {
            return response()->json(['message' => 'User not found in this project.'], 404);
        }

        if ($targetMember->pivot->role === 'owner') {
            return response()->json(['message' => 'Cannot change the role of the project owner.'], 403);
        }

        $targetMember->pivot->update(['role'=>$request->role]);

        return response()->json(['message' => 'Member role changed successfully.']);
    }
    public function removeMember(Request $request, Project $project, User $user)
    {
        $currentUser = $request->user();
        $currentRole = $project->users()
            ->where('user_id', $currentUser->id)
            ->first()
            ->pivot
            ->role ?? null;

        if (!roleCan($currentRole, 'remove_member')) {
            return response()->json(['message' => 'Not authorized to remove members.'], 401);
        }

        $targetMember = $project->users()->where('user_id', $user->id)->first();

        if (!$targetMember) {
            return response()->json(['message' => 'User not found in this project.'], 404);
        }

        if ($targetMember->pivot->role === 'owner') {
            return response()->json(['message' => 'Cannot remove the project owner.'], 403);
        }

        $project->users()->detach($user->id);

        return response()->json(['message' => 'Member removed successfully.']);
    }

}
