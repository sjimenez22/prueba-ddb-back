<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusTask extends Model
{
    protected $primaryKey = 'pk_status';

    protected $fillable = ['pk_status', 'c_name'];
}
