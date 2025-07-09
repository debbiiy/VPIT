<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class VpitFin extends Model
{
    protected $table = 'tbl_vpit_fin';
    protected $fillable = ['nobkt', 'vendor', 'amount', 'invoice', 'file', 'received_date', 'payment_date', 'payment_invoice', 'is_status', 'created_by'];
    public $timestamps = false; 

    public function details(){
        return $this->hasMany(VpitFinDetail::class, 'nobkt', 'nobkt');
    }
    public function vendorNameFromJobOrderCost(){
        return $this->hasOne(JobOrderCost::class, 'vendor_code', 'vendor');
    }

}
