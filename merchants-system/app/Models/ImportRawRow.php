<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportRawRow extends Model
{
    protected $fillable = [
        'source_file',
        'source_row_number',
        'sheet_name',
        'header_row',
        'raw_row',
        'membership_no',
        'organization_name',
        'commercial_reg_no',
        'org_national_no',
    ];

    protected $casts = [
        'header_row' => 'array',
        'raw_row' => 'array',
    ];
}
