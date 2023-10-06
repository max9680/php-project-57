<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Task extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function status(): HasOne
    {
//        return $this->belongsTo(TaskStatus::class, 'status_id', 'id');
        return $this->hasOne(TaskStatus::class, 'id', 'status_id');
    }

    public function created_by_user(): HasOne
    {
//        return $this->belongsTo(User::class, 'created_by_id', 'id');
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }

    public function assigned_to_user(): HasOne
    {
//        return $this->belongsTo(User::class, 'assigned_to_id', 'id');
        return $this->hasOne(User::class, 'id', 'assigned_to_id');
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class, 'label_tasks', 'task_id', 'label_id');
    }
}
