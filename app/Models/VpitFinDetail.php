<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VpitFinDetail extends Model
{
    protected $table = 'tbl_vpit_fin_detail';
    protected $fillable = ['nobkt', 'jo_code', 'container_no'];

    public function suratJalan()
    {
        return $this->belongsTo(VpitDoc::class, 'container_no', 'container_no');
    }

    public function jobOrder()
    {
        return $this->belongsTo(JobOrder::class, 'jo_code', 'code');
    }
    public $timestamps = false;

    public function jobCost()
    {
        return $this->belongsTo(JobOrderCost::class, 'jo_code', 'jo_code');
    }
}

