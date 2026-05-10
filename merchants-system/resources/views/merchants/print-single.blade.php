<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة بطاقة التاجر</title>
    <style>
        :root {
            --border: #d9dee7;
            --muted: #6b7280;
            --title: #0f172a;
            --bg-soft: #f8fafc;
            --bg-badge: #eef2f7;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            font-family: Tahoma, Arial, sans-serif;
            direction: rtl;
            color: #111827;
            background: #ffffff;
        }

        body {
            padding: 10px;
            font-size: 12px;
            line-height: 1.35;
        }

        .print-toolbar {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
        }

        .print-toolbar button {
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            font-size: 12px;
        }

        .sheet {
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }

        .sheet-header {
            padding: 10px 14px;
            border-bottom: 1px solid var(--border);
            background: #fff;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            flex-wrap: wrap;
        }

        .sheet-title {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: var(--title);
            line-height: 1.2;
        }

        .sheet-subtitle {
            margin: 3px 0 0;
            color: var(--muted);
            font-size: 12px;
        }

        .sheet-meta {
            text-align: left;
            font-size: 11px;
            color: var(--muted);
            line-height: 1.6;
            min-width: 150px;
        }

        .status-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            padding: 8px 14px 0;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid transparent;
            background: var(--bg-badge);
        }

        .badge-success {
            background: #ecfdf5;
            color: #166534;
            border-color: #bbf7d0;
        }

        .badge-secondary {
            background: #f1f5f9;
            color: #475569;
            border-color: #cbd5e1;
        }

        .section {
            padding: 10px 14px;
            border-top: 1px solid var(--border);
        }

        .section-title {
            margin: 0 0 8px;
            font-size: 15px;
            font-weight: 700;
            color: var(--title);
        }

        .grid-4 {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .field {
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--bg-soft);
            padding: 8px 10px;
            min-height: 56px;
        }

        .field-label {
            font-size: 10px;
            color: var(--muted);
            margin-bottom: 4px;
            font-weight: 700;
            line-height: 1.2;
        }

        .field-value {
            font-size: 14px;
            line-height: 1.45;
            word-break: break-word;
            white-space: pre-wrap;
        }

        .field-value.sm {
            font-size: 12px;
        }

        .field-value.xs {
            font-size: 11px;
        }

        .field.span-2 {
            grid-column: span 2;
        }

        .field.span-3 {
            grid-column: span 3;
        }

        .contact-box {
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--bg-soft);
            padding: 8px 10px;
            min-height: 74px;
        }

        .contact-items {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .contact-items li {
            margin-bottom: 4px;
            padding-bottom: 4px;
            border-bottom: 1px dashed #d9e0e8;
            word-break: break-word;
            font-size: 11px;
            line-height: 1.4;
        }

        .contact-items li:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: 0;
        }

        .empty {
            color: var(--muted);
            font-size: 11px;
        }

        .footer-note {
            padding: 8px 14px 10px;
            color: var(--muted);
            font-size: 10px;
            border-top: 1px solid var(--border);
        }

        @media print {
            body {
                padding: 0;
                font-size: 11px;
            }

            .print-toolbar {
                display: none;
            }

            .sheet {
                border: 0;
                border-radius: 0;
            }

            .section,
            .field,
            .contact-box {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            @page {
                size: A4 landscape;
                margin: 7mm;
            }
        }

        @media (max-width: 1100px) {
            .grid-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .grid-3 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
</head>
<body>
@php
    $phones = $merchant->phones?->pluck('phone')?->filter()?->values() ?? collect();
    $mobiles = $merchant->mobiles?->pluck('mobile')?->filter()?->values() ?? collect();
    $emails = $merchant->emails?->pluck('email')?->filter()?->values() ?? collect();
    $faxes = $merchant->faxes?->pluck('fax')?->filter()?->values() ?? collect();

    $fmt = fn ($date) => $date ? \Illuminate\Support\Carbon::parse($date)->format('Y-m-d') : '-';
@endphp

<div class="print-toolbar">
    <button onclick="window.print()">طباعة</button>
    <button onclick="window.close()">إغلاق</button>
</div>

<div class="sheet">
    <div class="sheet-header">
        <div>
            <h1 class="sheet-title">بطاقة بيانات التاجر</h1>
        </div>

        <div class="sheet-meta">
            <div>الرقم الداخلي: {{ $merchant->id }}</div>
            <div>تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</div>
        </div>
    </div>

    <div class="status-row">
            <span class="badge {{ $merchant->contacted ? 'badge-success' : 'badge-secondary' }}">
                {{ $merchant->contacted ? 'تم التواصل' : 'لم يتم التواصل' }}
            </span>

        <span class="badge {{ $merchant->invited ? 'badge-success' : 'badge-secondary' }}">
                {{ $merchant->invited ? 'تمت الدعوة' : 'لم تتم الدعوة' }}
            </span>
    </div>

    <div class="section">
        <h2 class="section-title">البيانات الأساسية</h2>

        <div class="grid-4">
            <div class="field">
                <div class="field-label">رقم العضوية</div>
                <div class="field-value">{{ $merchant->membership_no ?: '-' }}</div>
            </div>
            <div class="field">
                <div class="field-label">الرقم الوطني</div>
                <div class="field-value">{{ $merchant->org_national_no ?: '-' }}</div>
            </div>
            <div class="field span-2">
                <div class="field-label">اسم الشركة</div>
                <div class="field-value sm">{{ $merchant->organization_name ?: '-' }}</div>
            </div>

            <div class="field">
                <div class="field-label">الاسم التجاري</div>
                <div class="field-value sm">{{ $merchant->commercial_name ?: '-' }}</div>
            </div>

            <div class="field">
                <div class="field-label">رقم السجل التجاري</div>
                <div class="field-value">{{ $merchant->commercial_reg_no ?: '-' }}</div>
            </div>



            <div class="field span-2">
                <div class="field-label">القطاع</div>
                <div class="field-value sm">{{ $merchant->sector ?: '-' }}</div>
            </div>

            <div class="field">
                <div class="field-label">تاريخ التسجيل</div>
                <div class="field-value">{{ $fmt($merchant->registered_date) }}</div>
            </div>

            <div class="field">
                <div class="field-label">تاريخ السجل التجاري</div>
                <div class="field-value">{{ $fmt($merchant->commercial_reg_date) }}</div>
            </div>

            <div class="field">
                <div class="field-label">تاريخ الاشتراك</div>
                <div class="field-value">{{ $fmt($merchant->sub_date) }}</div>
            </div>

            <div class="field">
                <div class="field-label">CCateID</div>
                <div class="field-value">{{ $merchant->ccate_id ?: '-' }}</div>
            </div>

            <div class="field span-2">
                <div class="field-label">المفوّض بالتوقيع</div>
                <div class="field-value sm">{{ $merchant->delegate_to_sign_on_management ?: '-' }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2 class="section-title">العنوان والمعلومات الإضافية</h2>

        <div class="grid-3">
            <div class="field span-2">
                <div class="field-label">العنوان</div>
                <div class="field-value sm">{{ $merchant->street ?: '-' }}</div>
            </div>

            <div class="field">
                <div class="field-label">صندوق البريد</div>
                <div class="field-value">{{ $merchant->po_box ?: '-' }}</div>
            </div>

            <div class="field">
                <div class="field-label">الرمز البريدي</div>
                <div class="field-value">{{ $merchant->zipcode ?: '-' }}</div>
            </div>

            <div class="field span-2">
                <div class="field-label">وصف الرمز البريدي</div>
                <div class="field-value sm">{{ $merchant->zipcode_desc ?: '-' }}</div>
            </div>

            <div class="field span-3">
                <div class="field-label">الوصف</div>
                <div class="field-value xs">{{ $merchant->description ?: '-' }}</div>
            </div>

            <div class="field span-3">
                <div class="field-label">الأعضاء / الشركاء</div>
                <div class="field-value xs">{{ $merchant->members ?: '-' }}</div>
            </div>

            <div class="field span-3">
                <div class="field-label">الملاحظات</div>
                <div class="field-value xs">{{ $merchant->notes ?: '-' }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2 class="section-title">وسائل الاتصال</h2>

        <div class="grid-4">
            <div class="contact-box">
                <div class="field-label">الهاتف</div>
                @if($phones->isNotEmpty())
                    <ul class="contact-items">
                        @foreach($phones as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty">لا يوجد</div>
                @endif
            </div>

            <div class="contact-box">
                <div class="field-label">الموبايل</div>
                @if($mobiles->isNotEmpty())
                    <ul class="contact-items">
                        @foreach($mobiles as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty">لا يوجد</div>
                @endif
            </div>

            <div class="contact-box">
                <div class="field-label">الإيميل</div>
                @if($emails->isNotEmpty())
                    <ul class="contact-items">
                        @foreach($emails as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty">لا يوجد</div>
                @endif
            </div>

            <div class="contact-box">
                <div class="field-label">الفاكس</div>
                @if($faxes->isNotEmpty())
                    <ul class="contact-items">
                        @foreach($faxes as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty">لا يوجد</div>
                @endif
            </div>
        </div>
    </div>

</div>

<script>
    window.onload = function () {
        window.print();
    };
</script>
</body>
</html>
