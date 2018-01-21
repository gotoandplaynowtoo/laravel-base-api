<?php

namespace App\BaseModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseCommonModel extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
