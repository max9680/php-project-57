<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskStatus extends Model
{
    use HasFactory;

    protected $guarded = false;

    public function tasks()
    {
        return $this->hasMany(Task::class, 'status_id', 'id');
    }
}
