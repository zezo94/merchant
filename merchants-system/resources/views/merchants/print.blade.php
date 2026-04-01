<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة التجار</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            direction: rtl;
            margin: 20px;
            color: #000;
        }

        h2 {
            margin-bottom: 15px;
        }

        .meta {
            margin-bottom: 15px;
            font-size: 13px;
            color: #333;
        }

        .no-print {
            margin-bottom: 20px;
        }

        button {
            padding: 8px 14px;
            margin-left: 8px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 12px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: right;
            vertical-align: top;
            word-break: break-word;
        }

        th {
            background: #f1f1f1;
            white-space: nowrap;
        }

        .empty {
            color: #666;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                margin: 0;
            }

            @page {
                size: auto;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()">طباعة</button>
    <button onclick="window.close()">إغلاق</button>
</div>

<h2>نتائج التجار</h2>
<div class="meta">
    عدد السجلات: {{ $merchants->count() }}
</div>

<table>
    <thead>
    <tr>
        @if(in_array('id', $columns)) <th>#</th> @endif
        @if(in_array('membership_no', $columns)) <th>رقم العضوية</th> @endif
        @if(in_array('organization_name', $columns)) <th>اسم الشركة</th> @endif
        @if(in_array('commercial_name', $columns)) <th>الاسم التجاري</th> @endif
        @if(in_array('commercial_reg_no', $columns)) <th>رقم السجل التجاري</th> @endif
        @if(in_array('org_national_no', $columns)) <th>الرقم الوطني</th> @endif
        @if(in_array('sector', $columns)) <th>القطاع</th> @endif
        @if(in_array('street', $columns)) <th>العنوان</th> @endif
        @if(in_array('description', $columns)) <th>الوصف</th> @endif
        @if(in_array('delegate', $columns)) <th>المفوّض بالتوقيع</th> @endif
        @if(in_array('members', $columns)) <th>الأعضاء / الشركاء</th> @endif
        @if(in_array('po_box', $columns)) <th>صندوق البريد</th> @endif
        @if(in_array('zipcode_desc', $columns)) <th>وصف الرمز البريدي</th> @endif
        @if(in_array('zipcode', $columns)) <th>الرمز البريدي</th> @endif
        @if(in_array('phones', $columns)) <th>الهاتف</th> @endif
        @if(in_array('mobiles', $columns)) <th>الموبايل</th> @endif
        @if(in_array('emails', $columns)) <th>الايميل</th> @endif
        @if(in_array('faxes', $columns)) <th>الفاكس</th> @endif
        @if(in_array('registered_date', $columns)) <th>تاريخ التسجيل</th> @endif
        @if(in_array('commercial_reg_date', $columns)) <th>تاريخ السجل التجاري</th> @endif
        @if(in_array('contacted', $columns)) <th>تم التواصل</th> @endif
        @if(in_array('invited', $columns)) <th>الدعوة</th> @endif
        @if(in_array('notes', $columns)) <th>ملاحظات</th> @endif
    </tr>
    </thead>

    <tbody>
    @forelse($merchants as $merchant)
        <tr>
            @if(in_array('id', $columns)) <td>{{ $merchant->id }}</td> @endif
            @if(in_array('membership_no', $columns)) <td>{{ $merchant->membership_no }}</td> @endif
            @if(in_array('organization_name', $columns)) <td>{{ $merchant->organization_name }}</td> @endif
            @if(in_array('commercial_name', $columns)) <td>{{ $merchant->commercial_name }}</td> @endif
            @if(in_array('commercial_reg_no', $columns)) <td>{{ $merchant->commercial_reg_no }}</td> @endif
            @if(in_array('org_national_no', $columns)) <td>{{ $merchant->org_national_no }}</td> @endif
            @if(in_array('sector', $columns)) <td>{{ $merchant->sector }}</td> @endif
            @if(in_array('street', $columns)) <td>{{ $merchant->street }}</td> @endif
            @if(in_array('description', $columns)) <td>{{ $merchant->description }}</td> @endif
            @if(in_array('delegate', $columns)) <td>{{ $merchant->delegate_to_sign_on_management }}</td> @endif
            @if(in_array('members', $columns)) <td>{{ $merchant->members }}</td> @endif
            @if(in_array('po_box', $columns)) <td>{{ $merchant->po_box }}</td> @endif
            @if(in_array('zipcode_desc', $columns)) <td>{{ $merchant->zipcode_desc }}</td> @endif
            @if(in_array('zipcode', $columns)) <td>{{ $merchant->zipcode }}</td> @endif
            @if(in_array('phones', $columns)) <td>{{ $merchant->phones->pluck('phone')->implode(' | ') }}</td> @endif
            @if(in_array('mobiles', $columns)) <td>{{ $merchant->mobiles->pluck('mobile')->implode(' | ') }}</td> @endif
            @if(in_array('emails', $columns)) <td>{{ $merchant->emails->pluck('email')->implode(' | ') }}</td> @endif
            @if(in_array('faxes', $columns)) <td>{{ $merchant->faxes->pluck('fax')->implode(' | ') }}</td> @endif
            @if(in_array('registered_date', $columns)) <td>{{ optional($merchant->registered_date)->format('Y-m-d') }}</td> @endif
            @if(in_array('commercial_reg_date', $columns)) <td>{{ optional($merchant->commercial_reg_date)->format('Y-m-d') }}</td> @endif
            @if(in_array('contacted', $columns)) <td>{{ $merchant->contacted ? 'نعم' : 'لا' }}</td> @endif
            @if(in_array('invited', $columns)) <td>{{ $merchant->invited ? 'نعم' : 'لا' }}</td> @endif
            @if(in_array('notes', $columns)) <td>{{ $merchant->notes }}</td> @endif
        </tr>
    @empty
        <tr>
            <td colspan="30" class="empty">لا توجد نتائج</td>
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
