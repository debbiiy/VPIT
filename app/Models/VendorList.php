<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorList extends Model
{
    protected $table = 'vendor_list';
    protected $fillable = ['vendor_code', 'vendor_name'];
}
