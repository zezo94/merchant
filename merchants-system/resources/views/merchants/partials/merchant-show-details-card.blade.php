<div class="card section-card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="section-title">البيانات الأساسية</h5>

        <div class="d-flex gap-2 flex-wrap">
            <span class="badge bg-{{ $merchant->contacted ? 'success' : 'secondary' }} badge-status">
                {{ $merchant->contacted ? 'تم التواصل' : 'لم يتم التواصل' }}
            </span>

            <span class="badge bg-{{ $merchant->invited ? 'primary' : 'secondary' }} badge-status">
                {{ $merchant->invited ? 'تمت الدعوة' : 'لم تتم الدعوة' }}
            </span>
        </div>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="info-label">رقم العضوية</div>
                <div class="info-value">{{ $merchant->membership_no ?: '-' }}</div>
            </div>

            <div class="col-md-4">
                <div class="info-label">اسم الشركة</div>
                <div class="info-value">{{ $merchant->organization_name ?: '-' }}</div>
            </div>

            <div class="col-md-4">
                <div class="info-label">الاسم التجاري</div>
                <div class="info-value">{{ $merchant->commercial_name ?: '-' }}</div>
            </div>

            <div class="col-md-4">
                <div class="info-label">رقم السجل التجاري</div>
                <div class="info-value">{{ $merchant->commercial_reg_no ?: '-' }}</div>
            </div>

            <div class="col-md-4">
                <div class="info-label">الرقم الوطني</div>
                <div class="info-value">{{ $merchant->org_national_no ?: '-' }}</div>
            </div>

            <div class="col-md-4">
                <div class="info-label">القطاع</div>
                <div class="info-value">{{ $merchant->sector ?: '-' }}</div>
            </div>

            <div class="col-md-4">
                <div class="info-label">تاريخ التسجيل</div>
                <div class="info-value">{{ optional($merchant->registered_date)->format('Y-m-d') ?: '-' }}</div>
            </div>

            <div class="col-md-4">
                <div class="info-label">تاريخ الاشتراك</div>
                <div class="info-value">{{ optional($merchant->sub_date)->format('Y-m-d') ?: '-' }}</div>
            </div>

            <div class="col-md-4">
                <div class="info-label">تاريخ السجل التجاري</div>
                <div class="info-value">{{ optional($merchant->commercial_reg_date)->format('Y-m-d') ?: '-' }}</div>
            </div>

            <div class="col-md-4">
                <div class="info-label">CCateID</div>
                <div class="info-value">{{ $merchant->ccate_id ?: '-' }}</div>
            </div>

            <div class="col-md-4">
                <div class="info-label">المفوّض بالتوقيع</div>
                <div class="info-value">{{ $merchant->delegate_to_sign_on_management ?: '-' }}</div>
            </div>

            <div class="col-md-4">
                <div class="info-label">الأعضاء / الشركاء</div>
                <div class="info-value">{{ $merchant->members ?: '-' }}</div>
            </div>

            <div class="col-12">
                <div class="info-label">الوصف</div>
                <div class="info-value">{{ $merchant->description ?: '-' }}</div>
            </div>

            <div class="col-12">
                <div class="info-label">ملاحظات</div>
                <div class="info-value">{{ $merchant->notes ?: '-' }}</div>
            </div>
        </div>
    </div>
</div>
