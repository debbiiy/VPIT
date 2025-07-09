<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VpitDoc extends Model
{
    use HasFactory;

    protected $table = 'tbl_vpit_doc';

    protected $guarded = [];

    public function jobOrder()
    {
        return $this->belongsTo(JobOrder::class, 'jo_code', 'code');
    }

    public function jobCost()
    {
        return $this->hasMany(JobOrderCost::class, 'jo_code', 'jo_code');
    }

    public function jobCostByContainer()
    {
        return $this->hasOne(JobOrderCost::class, 'jo_code', 'jo_code')
                    ->whereColumn('container_no', 'container_no');
    }
}

