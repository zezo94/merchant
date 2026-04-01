@extends('layouts.app')

@php
    $pageTitle = 'عرض بيانات التاجر';
@endphp

@push('styles')
    <style>
        .section-card {
            border-radius: 12px;
        }
        .section-title {
            font-weight: 700;
            margin-bottom: 0;
        }
        .info-label {
            font-weight: 700;
            color: #495057;
            margin-bottom: 4px;
        }
        .info-value {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 10px 12px;
            min-height: 46px;
        }
        .contact-box {
            background: #f8f9fa;
            border: 1px dashed #ced4da;
            border-radius: 10px;
            padding: 12px;
            height: 100%;
        }
        .badge-status {
            font-size: 0.9rem;
            padding: 8px 12px;
            border-radius: 999px;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="mb-1">عرض بيانات التاجر</h2>
                <p class="text-muted mb-0">تفاصيل كاملة للسجل ووسائل الاتصال والحالة</p>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                @can('edit merchants')
                    <a href="{{ route('merchants.edit', $merchant->id) }}" class="btn btn-primary">تعديل</a>
                @endcan

                <a href="{{ route('merchants.index') }}" class="btn btn-outline-secondary">رجوع للقائمة</a>

                @can('delete merchants')
                    <form action="{{ route('merchants.destroy', $merchant->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('هل أنت متأكد من حذف هذا السجل؟')">
                            حذف
                        </button>
                    </form>
                @endcan
            </div>
        </div>

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

        <div class="card section-card">
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
    </div>
@endsection
