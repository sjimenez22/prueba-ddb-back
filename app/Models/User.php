<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'pk_user';

    protected $fillable = ['pk_user', 'c_name', 'c_email', 'c_password', 'fk_role'];
    protected $hidden = ['c_password', 'fk_role'];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'fk_role', 'pk_role');
    }
}
