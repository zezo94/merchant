<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantFax extends Model
{
    protected $table = 'merchant_faxes';

    protected $fillable = [
        'merchant_id',
        'fax',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
