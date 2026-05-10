@extends('layouts.app')

@php
    $pageTitle = 'عرض بيانات التاجر';

    $fieldLabels = [
        'organization_name' => 'اسم الشركة',
        'membership_no' => 'رقم العضوية',
        'commercial_name' => 'الاسم التجاري',
        'commercial_reg_no' => 'رقم السجل التجاري',
        'org_national_no' => 'الرقم الوطني',
        'sector' => 'القطاع',
        'street' => 'العنوان',
        'description' => 'الوصف',
        'delegate_to_sign_on_management' => 'المفوّض بالتوقيع',
        'members' => 'الأعضاء / الشركاء',
        'po_box' => 'صندوق البريد',
        'zipcode_desc' => 'وصف الرمز البريدي',
        'zipcode' => 'الرمز البريدي',
        'registered_date' => 'تاريخ التسجيل',
        'commercial_reg_date' => 'تاريخ السجل التجاري',
        'contacted' => 'تم التواصل',
        'invited' => 'تمت الدعوة',
        'notes' => 'ملاحظات',
        'sub_date' => 'تاريخ الاشتراك',
        'ccate_id' => 'CCateID',
    ];

    $actionTitles = [
        'merchant_created' => 'إنشاء التاجر',
        'merchant_updated' => 'تعديل بيانات التاجر',
        'merchant_deleted' => 'حذف التاجر',
        'merchant_inline_updated' => 'تحديث سريع',
    ];

    $actionColors = [
        'merchant_created' => 'primary',
        'merchant_updated' => 'warning',
        'merchant_deleted' => 'danger',
        'merchant_inline_updated' => 'info',
    ];

    $formatValue = function ($value) {
        if ($value === null || $value === '') {
            return '-';
        }

        if ($value === true || $value === 1 || $value === '1') {
            return 'نعم';
        }

        if ($value === false || $value === 0 || $value === '0') {
            return 'لا';
        }

        return (string) $value;
    };
@endphp

@section('body_class', 'app-body merchant-show-page')

@section('content')
    <section class="merchant-show-section">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="mb-1">عرض بيانات التاجر</h2>
                <p class="text-muted mb-0">تفاصيل كاملة للسجل ووسائل الاتصال والحالة</p>
            </div>

            @include('merchants.partials.merchant-show-page-actions', ['merchant' => $merchant])
        </div>

        @include('merchants.partials.merchant-show-details-card', ['merchant' => $merchant])
        @include('merchants.partials.merchant-show-address-card', ['merchant' => $merchant])
        @include('merchants.partials.merchant-show-contacts-card', ['merchant' => $merchant])

        @if(auth()->user()?->is_root)
            @include('merchants.partials.history.merchant-history-card', [
                'merchantTimeline' => $merchantTimeline ?? collect(),
                'fieldLabels' => $fieldLabels,
                'actionTitles' => $actionTitles,
                'actionColors' => $actionColors,
                'formatValue' => $formatValue,
            ])
        @endif
    </section>
@endsection
