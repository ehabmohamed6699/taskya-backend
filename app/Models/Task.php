<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //
    protected $fillable = ['title', 'description', 'due_date', 'priority', 'completed', 'user_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
