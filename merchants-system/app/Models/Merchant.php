<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Merchant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'membership_no',
        'organization_name',
        'registered_date',
        'sub_date',
        'description',
        'org_national_no',
        'commercial_reg_no',
        'commercial_reg_date',
        'commercial_name',
        'sector',
        'ccate_id',
        'delegate_to_sign_on_management',
        'members',
        'street',
        'po_box',
        'zipcode_desc',
        'zipcode',
        'contacted',
        'invited',
        'notes',
    ];

    protected $casts = [
        'registered_date' => 'date',
        'sub_date' => 'date',
        'commercial_reg_date' => 'date',
        'contacted' => 'boolean',
        'invited' => 'boolean',
    ];

    public function phones()
    {
        return $this->hasMany(MerchantPhone::class);
    }

    public function mobiles()
    {
        return $this->hasMany(MerchantMobile::class);
    }

    public function emails()
    {
        return $this->hasMany(MerchantEmail::class);
    }

    public function faxes()
    {
        return $this->hasMany(MerchantFax::class);
    }
}
