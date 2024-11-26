<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'pk_user';

    protected $fillable = ['pk_user', 'c_name', 'c_email', 'c_password', 'fk_role', 'c_password'];
    protected $hidden = ['fk_role'];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'fk_role', 'pk_role');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'fk_user');
    }

    public function tasksCreator(): HasMany
    {
        return $this->hasMany(Task::class, 'fk_user_creator');
    }

    public function tasksResponsible(): HasMany
    {
        return $this->hasMany(Task::class, 'fk_user_responsible');
    }
}
