<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MerchantsExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected Collection $merchants,
        protected array $columns
    ) {}

    public function headings(): array
    {
        $map = [
            'id' => '#',
            'membership_no' => 'رقم العضوية',
            'organization_name' => 'اسم الشركة',
            'commercial_name' => 'الاسم التجاري',
            'commercial_reg_no' => 'رقم السجل التجاري',
            'org_national_no' => 'الرقم الوطني',
            'sector' => 'القطاع',
            'street' => 'العنوان',
            'description' => 'الوصف',
            'delegate' => 'المفوّض بالتوقيع',
            'members' => 'الأعضاء / الشركاء',
            'po_box' => 'صندوق البريد',
            'zipcode_desc' => 'وصف الرمز البريدي',
            'zipcode' => 'الرمز البريدي',
            'phones' => 'الهاتف',
            'mobiles' => 'الموبايل',
            'emails' => 'الايميل',
            'faxes' => 'الفاكس',
            'registered_date' => 'تاريخ التسجيل',
            'commercial_reg_date' => 'تاريخ السجل التجاري',
            'contacted' => 'تم التواصل',
            'invited' => 'الدعوة',
            'notes' => 'ملاحظات',
        ];

        return collect($this->columns)
            ->map(fn ($column) => $map[$column] ?? $column)
            ->all();
    }

    public function collection(): Collection
    {
        return $this->merchants->map(function ($merchant) {
            return collect($this->columns)->map(function ($column) use ($merchant) {
                return match ($column) {
                    'id' => $merchant->id,
                    'membership_no' => $merchant->membership_no,
                    'organization_name' => $merchant->organization_name,
                    'commercial_name' => $merchant->commercial_name,
                    'commercial_reg_no' => $merchant->commercial_reg_no,
                    'org_national_no' => $merchant->org_national_no,
                    'sector' => $merchant->sector,
                    'street' => $merchant->street,
                    'description' => $merchant->description,
                    'delegate' => $merchant->delegate_to_sign_on_management,
                    'members' => $merchant->members,
                    'po_box' => $merchant->po_box,
                    'zipcode_desc' => $merchant->zipcode_desc,
                    'zipcode' => $merchant->zipcode,
                    'phones' => $merchant->phones->pluck('phone')->implode(' | '),
                    'mobiles' => $merchant->mobiles->pluck('mobile')->implode(' | '),
                    'emails' => $merchant->emails->pluck('email')->implode(' | '),
                    'faxes' => $merchant->faxes->pluck('fax')->implode(' | '),
                    'registered_date' => optional($merchant->registered_date)->format('Y-m-d'),
                    'commercial_reg_date' => optional($merchant->commercial_reg_date)->format('Y-m-d'),
                    'contacted' => $merchant->contacted ? 'نعم' : 'لا',
                    'invited' => $merchant->invited ? 'نعم' : 'لا',
                    'notes' => $merchant->notes,
                    default => '',
                };
            })->all();
        });
    }
}
