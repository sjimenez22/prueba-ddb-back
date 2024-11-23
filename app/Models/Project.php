<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $primaryKey = 'pk_project';

    protected $fillable = ['pk_project', 'c_name', 'fk_user'];
    protected $hidden = ['fk_user'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fk_user', 'pk_user');
    }
}
