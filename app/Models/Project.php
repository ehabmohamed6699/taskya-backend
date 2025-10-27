<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //
    protected $fillable = ['name'];

    public function users(){
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }
    public function tasks(){
        return $this->hasMany(Task::class);
    }

    public function hasPermission(User $user, string $action): bool
    {
        $role = strtolower($this->users()
            ->where('user_id', $user->id)
            ->value('role'));

        if (!$role) {
            return false; // المستخدم مش عضو في المشروع
        }

        $rolePermissions = config("project_roles.role_permissions.$role", []);
        
        // لو الدور عنده '*' معناها كل الصلاحيات
        if (in_array('*', $rolePermissions)) {
            return true;
        }

        return in_array($action, $rolePermissions);
    }
}

