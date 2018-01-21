<?php

namespace App\BaseModels;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class BaseAuthenticatableModel extends Authenticatable
{
    use SoftDeletes, Notifiable;
    protected $dates = ['deleted_at'];
}
