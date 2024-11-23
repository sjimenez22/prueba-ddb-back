<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $primaryKey = 'pk_role';

    protected $fillable = ['pk_role', 'c_name'];
}
