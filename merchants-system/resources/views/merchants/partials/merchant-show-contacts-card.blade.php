<div class="card section-card mb-4">
    <div class="card-header bg-white">
        <h5 class="section-title">وسائل الاتصال</h5>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="contact-box">
                    <div class="info-label mb-2">أرقام الهاتف</div>
                    @forelse($merchant->phones as $phone)
                        <div class="mb-1">{{ $phone->phone }}</div>
                    @empty
                        <div class="text-muted">لا يوجد</div>
                    @endforelse
                </div>
            </div>

            <div class="col-md-3">
                <div class="contact-box">
                    <div class="info-label mb-2">أرقام الموبايل</div>
                    @forelse($merchant->mobiles as $mobile)
                        <div class="mb-1">{{ $mobile->mobile }}</div>
                    @empty
                        <div class="text-muted">لا يوجد</div>
                    @endforelse
                </div>
            </div>

            <div class="col-md-3">
                <div class="contact-box">
                    <div class="info-label mb-2">الإيميلات</div>
                    @forelse($merchant->emails as $email)
                        <div class="mb-1">{{ $email->email }}</div>
                    @empty
                        <div class="text-muted">لا يوجد</div>
                    @endforelse
                </div>
            </div>

            <div class="col-md-3">
                <div class="contact-box">
                    <div class="info-label mb-2">الفاكس</div>
                    @forelse($merchant->faxes as $fax)
                        <div class="mb-1">{{ $fax->fax }}</div>
                    @empty
                        <div class="text-muted">لا يوجد</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
