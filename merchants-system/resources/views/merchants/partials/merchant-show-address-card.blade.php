<div class="card section-card mb-4">
    <div class="card-header bg-white">
        <h5 class="section-title">العنوان والرموز البريدية</h5>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="info-label">العنوان</div>
                <div class="info-value">{{ $merchant->street ?: '-' }}</div>
            </div>

            <div class="col-md-2">
                <div class="info-label">صندوق البريد</div>
                <div class="info-value">{{ $merchant->po_box ?: '-' }}</div>
            </div>

            <div class="col-md-2">
                <div class="info-label">الرمز البريدي</div>
                <div class="info-value">{{ $merchant->zipcode ?: '-' }}</div>
            </div>

            <div class="col-md-2">
                <div class="info-label">وصف الرمز البريدي</div>
                <div class="info-value">{{ $merchant->zipcode_desc ?: '-' }}</div>
            </div>
        </div>
    </div>
</div>
