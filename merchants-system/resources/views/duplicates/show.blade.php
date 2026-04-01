@extends('layouts.app')

@php
    $pageTitle = 'تفاصيل التكرار';
@endphp

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">السجلات المكررة</h2>
                <p class="text-muted mb-0">
                    النوع:
                    <strong>{{ $type }}</strong>
                    —
                    القيمة:
                    <strong>{{ $value }}</strong>
                </p>
            </div>

            <a href="{{ route('duplicates.index', ['type' => $type]) }}" class="btn btn-secondary">رجوع</a>
        </div>

        @foreach($merchants as $merchant)
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>السجل #{{ $merchant->id }}</strong>

                    <div class="d-flex gap-2">
                        <a href="{{ route('merchants.show', $merchant->id) }}" class="btn btn-sm btn-outline-info">عرض</a>
                        <a href="{{ route('merchants.edit', $merchant->id) }}" class="btn btn-sm btn-outline-primary">تعديل</a>

                        <form method="POST" action="{{ route('duplicates.destroy', $merchant->id) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('هل أنت متأكد من حذف هذا السجل؟')">
                                حذف
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><strong>رقم العضوية:</strong> {{ $merchant->membership_no }}</div>
                        <div class="col-md-4"><strong>اسم الشركة:</strong> {{ $merchant->organization_name }}</div>
                        <div class="col-md-4"><strong>الاسم التجاري:</strong> {{ $merchant->commercial_name }}</div>

                        <div class="col-md-4"><strong>رقم السجل التجاري:</strong> {{ $merchant->commercial_reg_no }}</div>
                        <div class="col-md-4"><strong>الرقم الوطني:</strong> {{ $merchant->org_national_no }}</div>
                        <div class="col-md-4"><strong>القطاع:</strong> {{ $merchant->sector }}</div>

                        <div class="col-md-6"><strong>العنوان:</strong> {{ $merchant->street }}</div>
                        <div class="col-md-6"><strong>المفوّض بالتوقيع:</strong> {{ $merchant->delegate_to_sign_on_management }}</div>

                        <div class="col-12"><strong>الأعضاء / الشركاء:</strong> {{ $merchant->members }}</div>
                        <div class="col-12"><strong>الوصف:</strong> {{ $merchant->description }}</div>
                        <div class="col-12"><strong>ملاحظات:</strong> {{ $merchant->notes }}</div>
                    </div>

                    <hr>

                    <div class="row g-4">
                        <div class="col-md-3">
                            <h6>الهاتف</h6>
                            @forelse($merchant->phones as $item)
                                <div>{{ $item->phone }}</div>
                            @empty
                                <div class="text-muted">لا يوجد</div>
                            @endforelse
                        </div>

                        <div class="col-md-3">
                            <h6>الموبايل</h6>
                            @forelse($merchant->mobiles as $item)
                                <div>{{ $item->mobile }}</div>
                            @empty
                                <div class="text-muted">لا يوجد</div>
                            @endforelse
                        </div>

                        <div class="col-md-3">
                            <h6>الايميل</h6>
                            @forelse($merchant->emails as $item)
                                <div>{{ $item->email }}</div>
                            @empty
                                <div class="text-muted">لا يوجد</div>
                            @endforelse
                        </div>

                        <div class="col-md-3">
                            <h6>الفاكس</h6>
                            @forelse($merchant->faxes as $item)
                                <div>{{ $item->fax }}</div>
                            @empty
                                <div class="text-muted">لا يوجد</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
