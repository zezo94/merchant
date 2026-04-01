<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantPhone extends Model
{
    protected $table = 'merchant_phones';

    protected $fillable = [
        'merchant_id',
        'phone',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
