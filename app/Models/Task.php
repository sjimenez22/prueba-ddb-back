<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $primaryKey = 'pk_task';

    protected $fillable = ['pk_task', 'c_name', 'c_description', 'd_completion', 'fk_project', 'fk_status', 'fk_user_responsible', 'fk_user_creator'];
    protected $hidden = ['fk_project', 'fk_status', 'fk_user_responsible', 'fk_user_creator'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'fk_project');
    }

    public function statusTask(): BelongsTo
    {
        return $this->belongsTo(StatusTask::class, 'fk_status');
    }

    public function userResponsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fk_user_responsible');
    }

    public function userCreator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fk_user_creator');
    }
}
