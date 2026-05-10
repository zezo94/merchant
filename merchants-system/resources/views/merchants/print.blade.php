<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة التجار</title>
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            direction: rtl;
            color: #111827;
            margin: 20px;
            background: #fff;
        }

        .print-header {
            margin-bottom: 18px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 12px;
        }

        .print-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .print-meta {
            font-size: 13px;
            color: #4b5563;
            line-height: 1.8;
        }

        .print-toolbar {
            margin-bottom: 16px;
        }

        .print-toolbar button {
            padding: 8px 14px;
            border: 1px solid #9ca3af;
            border-radius: 8px;
            background: #f9fafb;
            cursor: pointer;
            margin-left: 8px;
        }

        .print-toolbar button:hover {
            background: #f3f4f6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            font-size: 12px;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: right;
            vertical-align: top;
            word-break: break-word;
        }

        th {
            background: #f3f4f6;
            font-weight: 700;
            white-space: nowrap;
        }

        .empty {
            text-align: center;
            color: #6b7280;
        }

        @media print {
            .print-toolbar {
                display: none;
            }

            body {
                margin: 0;
            }

            @page {
                margin: 10mm;
                size: landscape;
            }
        }
    </style>
</head>
<body>
@php
    $columnLabels = [
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
@endphp

<div class="print-toolbar">
    <button onclick="window.print()">طباعة</button>
    <button onclick="window.close()">إغلاق</button>
</div>

<div class="print-header">
    <div class="print-title">تقرير التجار</div>
    <div class="print-meta">
        <div>عدد السجلات: {{ $merchants->count() }}</div>
        <div>تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</div>
    </div>
</div>

<table>
    <thead>
    <tr>
        @foreach($columns as $column)
            <th>{{ $columnLabels[$column] ?? $column }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @forelse($merchants as $merchant)
        <tr>
            @foreach($columns as $column)
                <td>
                    @switch($column)
                        @case('id') {{ $merchant->id }} @break
                        @case('membership_no') {{ $merchant->membership_no ?: '-' }} @break
                        @case('organization_name') {{ $merchant->organization_name ?: '-' }} @break
                        @case('commercial_name') {{ $merchant->commercial_name ?: '-' }} @break
                        @case('commercial_reg_no') {{ $merchant->commercial_reg_no ?: '-' }} @break
                        @case('org_national_no') {{ $merchant->org_national_no ?: '-' }} @break
                        @case('sector') {{ $merchant->sector ?: '-' }} @break
                        @case('street') {{ $merchant->street ?: '-' }} @break
                        @case('description') {{ $merchant->description ?: '-' }} @break
                        @case('delegate') {{ $merchant->delegate_to_sign_on_management ?: '-' }} @break
                        @case('members') {{ $merchant->members ?: '-' }} @break
                        @case('po_box') {{ $merchant->po_box ?: '-' }} @break
                        @case('zipcode_desc') {{ $merchant->zipcode_desc ?: '-' }} @break
                        @case('zipcode') {{ $merchant->zipcode ?: '-' }} @break
                        @case('phones') {{ $merchant->phones->pluck('phone')->implode(' | ') ?: '-' }} @break
                        @case('mobiles') {{ $merchant->mobiles->pluck('mobile')->implode(' | ') ?: '-' }} @break
                        @case('emails') {{ $merchant->emails->pluck('email')->implode(' | ') ?: '-' }} @break
                        @case('faxes') {{ $merchant->faxes->pluck('fax')->implode(' | ') ?: '-' }} @break
                        @case('registered_date') {{ optional($merchant->registered_date)->format('Y-m-d') ?: '-' }} @break
                        @case('commercial_reg_date') {{ optional($merchant->commercial_reg_date)->format('Y-m-d') ?: '-' }} @break
                        @case('contacted') {{ $merchant->contacted ? 'نعم' : 'لا' }} @break
                        @case('invited') {{ $merchant->invited ? 'نعم' : 'لا' }} @break
                        @case('notes') {{ $merchant->notes ?: '-' }} @break
                        @default -
                    @endswitch
                </td>
            @endforeach
        </tr>
    @empty
        <tr>
            <td colspan="{{ max(count($columns), 1) }}" class="empty">لا توجد نتائج</td>
        </tr>
    @endforelse
    </tbody>
</table>

<script>
    window.onload = function () {
        window.print();
    };
</script>
</body>
</html>
