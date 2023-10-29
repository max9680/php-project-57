<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Label extends Model
{
    use HasFactory;

    protected $guarded = false;

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'label_tasks', 'label_id', 'task_id');
    }
}
