<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantMobile extends Model
{
    protected $table = 'merchant_mobiles';

    protected $fillable = [
        'merchant_id',
        'mobile',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
