<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobOrderCost extends Model
{
    protected $table = 'tbl_joborder_cost';
    public $timestamps = false;

    public function vpitDoc()
    {
        return $this->belongsTo(VpitDoc::class, 'jo_code', 'jo_code');
    }
}
