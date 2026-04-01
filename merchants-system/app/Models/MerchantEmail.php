<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantEmail extends Model
{
    protected $table = 'merchant_emails';

    protected $fillable = [
        'merchant_id',
        'email',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
