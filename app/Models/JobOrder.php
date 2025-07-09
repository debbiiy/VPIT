<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobOrder extends Model
{
    protected $table = 'tbl_joborder';
    protected $primaryKey = 'code';

    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;
}


