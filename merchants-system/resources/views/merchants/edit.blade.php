@extends('layouts.app')

@php
    $pageTitle = 'تعديل بيانات التاجر';
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

        .dynamic-item {
            background: #f8f9fa;
            border: 1px dashed #ced4da;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 10px;
        }
    </style>
@endpush

@section('content')
    @can('edit merchants')
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="mb-1">تعديل بيانات التاجر</h2>
                    <p class="text-muted mb-0">عدّل البيانات الأساسية ووسائل الاتصال ثم احفظ التغييرات</p>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('merchants.show', $merchant->id) }}" class="btn btn-outline-info">عرض</a>
                    <a href="{{ route('merchants.index') }}" class="btn btn-outline-secondary">رجوع للقائمة</a>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>يوجد أخطاء في الإدخال:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('merchants.update', $merchant->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card section-card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="section-title">البيانات الأساسية</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">رقم العضوية</label>
                                <input type="text" name="membership_no" class="form-control" value="{{ old('membership_no', $merchant->membership_no) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">اسم الشركة <span class="text-danger">*</span></label>
                                <input type="text" name="organization_name" class="form-control" value="{{ old('organization_name', $merchant->organization_name) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">الاسم التجاري</label>
                                <input type="text" name="commercial_name" class="form-control" value="{{ old('commercial_name', $merchant->commercial_name) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">رقم السجل التجاري</label>
                                <input type="text" name="commercial_reg_no" class="form-control" value="{{ old('commercial_reg_no', $merchant->commercial_reg_no) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">الرقم الوطني</label>
                                <input type="text" name="org_national_no" class="form-control" value="{{ old('org_national_no', $merchant->org_national_no) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">القطاع</label>
                                <input type="text" name="sector" class="form-control" value="{{ old('sector', $merchant->sector) }}" placeholder="اكتب اسم القطاع">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">تاريخ التسجيل</label>
                                <input type="date" name="registered_date" class="form-control" value="{{ old('registered_date', optional($merchant->registered_date)->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">تاريخ الاشتراك</label>
                                <input type="date" name="sub_date" class="form-control" value="{{ old('sub_date', optional($merchant->sub_date)->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">تاريخ السجل التجاري</label>
                                <input type="date" name="commercial_reg_date" class="form-control" value="{{ old('commercial_reg_date', optional($merchant->commercial_reg_date)->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">CCateID</label>
                                <input type="text" name="ccate_id" class="form-control" value="{{ old('ccate_id', $merchant->ccate_id) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">المفوّض بالتوقيع</label>
                                <input type="text" name="delegate_to_sign_on_management" class="form-control" value="{{ old('delegate_to_sign_on_management', $merchant->delegate_to_sign_on_management) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">الأعضاء / الشركاء</label>
                                <input type="text" name="members" class="form-control" value="{{ old('members', $merchant->members) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" rows="3" class="form-control">{{ old('description', $merchant->description) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" rows="3" class="form-control">{{ old('notes', $merchant->notes) }}</textarea>
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
                                <label class="form-label">العنوان</label>
                                <input type="text" name="street" class="form-control" value="{{ old('street', $merchant->street) }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">صندوق البريد</label>
                                <input type="text" name="po_box" class="form-control" value="{{ old('po_box', $merchant->po_box) }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">الرمز البريدي</label>
                                <input type="text" name="zipcode" class="form-control" value="{{ old('zipcode', $merchant->zipcode) }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">وصف الرمز البريدي</label>
                                <input type="text" name="zipcode_desc" class="form-control" value="{{ old('zipcode_desc', $merchant->zipcode_desc) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card section-card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="section-title">بيانات الحالة</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="contacted" class="form-check-input" id="contacted" {{ old('contacted', $merchant->contacted) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="contacted">تم التواصل</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="invited" class="form-check-input" id="invited" {{ old('invited', $merchant->invited) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="invited">تمت الدعوة</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $phones = old('phones', $merchant->phones->pluck('phone')->toArray());
                    $mobiles = old('mobiles', $merchant->mobiles->pluck('mobile')->toArray());
                    $emails = old('emails', $merchant->emails->pluck('email')->toArray());
                    $faxes = old('faxes', $merchant->faxes->pluck('fax')->toArray());

                    if (empty($phones)) $phones = [''];
                    if (empty($mobiles)) $mobiles = [''];
                    if (empty($emails)) $emails = [''];
                    if (empty($faxes)) $faxes = [''];
                @endphp

                <div class="card section-card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="section-title">أرقام الهاتف</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addField('phones-container', 'phones[]', 'رقم الهاتف', 'text')">
                            إضافة هاتف
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="phones-container">
                            @foreach($phones as $phone)
                                <div class="dynamic-item d-flex gap-2">
                                    <input type="text" name="phones[]" class="form-control" placeholder="رقم الهاتف" value="{{ $phone }}">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">حذف</button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card section-card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="section-title">أرقام الموبايل</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addField('mobiles-container', 'mobiles[]', 'رقم الموبايل', 'text')">
                            إضافة موبايل
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="mobiles-container">
                            @foreach($mobiles as $mobile)
                                <div class="dynamic-item d-flex gap-2">
                                    <input type="text" name="mobiles[]" class="form-control" placeholder="رقم الموبايل" value="{{ $mobile }}">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">حذف</button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card section-card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="section-title">الإيميلات</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addField('emails-container', 'emails[]', 'البريد الإلكتروني', 'email')">
                            إضافة إيميل
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="emails-container">
                            @foreach($emails as $email)
                                <div class="dynamic-item d-flex gap-2">
                                    <input type="email" name="emails[]" class="form-control" placeholder="البريد الإلكتروني" value="{{ $email }}">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">حذف</button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card section-card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="section-title">الفاكس</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addField('faxes-container', 'faxes[]', 'رقم الفاكس', 'text')">
                            إضافة فاكس
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="faxes-container">
                            @foreach($faxes as $fax)
                                <div class="dynamic-item d-flex gap-2">
                                    <input type="text" name="faxes[]" class="form-control" placeholder="رقم الفاكس" value="{{ $fax }}">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">حذف</button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-success">حفظ التعديلات</button>
                    <a href="{{ route('merchants.show', $merchant->id) }}" class="btn btn-outline-info">عرض السجل</a>
                    <a href="{{ route('merchants.index') }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    @else
        <div class="alert alert-danger">ليس لديك صلاحية تعديل هذا السجل.</div>
    @endcan
@endsection

@push('scripts')
    <script>
        function addField(containerId, inputName, placeholder, inputType = 'text') {
            const container = document.getElementById(containerId);

            const wrapper = document.createElement('div');
            wrapper.className = 'dynamic-item d-flex gap-2';

            wrapper.innerHTML = `
            <input type="${inputType}" name="${inputName}" class="form-control" placeholder="${placeholder}">
            <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">حذف</button>
        `;

            container.appendChild(wrapper);
        }

        function removeField(button) {
            button.parentElement.remove();
        }
    </script>
@endpush
