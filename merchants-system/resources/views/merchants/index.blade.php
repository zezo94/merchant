@extends('layouts.app')

@php
    $pageTitle = 'إدارة التجار';
@endphp

@push('styles')
    <style>
        .table thead th { white-space: nowrap; }
        .small-badge { font-size: 0.8rem; }
        .filter-label { font-weight: 600; margin-bottom: 0.35rem; }
        .actions-cell { white-space: nowrap; min-width: 250px; }
        .table-responsive { min-height: 300px; }
        .sortable-link {
            color: inherit;
            text-decoration: none;
        }
        .sortable-link:hover {
            text-decoration: underline;
        }
        .inline-notes {
            min-width: 220px;
        }
        .row-selector-cell {
            width: 50px;
            text-align: center;
        }

        .accordion-button {
            font-weight: 700;
        }

        .accordion-button:not(.collapsed) {
            background-color: #f8f9fa;
            color: #212529;
            box-shadow: none;
        }

        .accordion-item {
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 10px;
            border: 1px solid #dee2e6;
        }

        #selected-count-badge {
            font-size: 0.95rem;
            padding: 10px 14px;
            border-radius: 999px;
        }

        .selected-preview-card {
            border: 1px solid #dee2e6;
            border-radius: 12px;
            background: #fff;
        }

        .selected-name-badge {
            background: #f8f9fa;
            color: #212529;
            border: 1px solid #dee2e6;
            border-radius: 999px;
            padding: 6px 10px;
            display: inline-block;
            margin: 0 6px 6px 0;
            font-size: 0.85rem;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        @php
            $canPrint = auth()->user()?->can('print merchants');
            $canExport = auth()->user()?->can('export merchants');
            $canSelect = $canPrint || $canExport;
            $canEdit = auth()->user()?->can('edit merchants');
            $canDelete = auth()->user()?->can('delete merchants');
            $canInlineEdit = $canEdit;
            $showActions = $canEdit || $canDelete;
        @endphp

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="mb-1">إدارة التجار</h2>
                <p class="text-muted mb-0">عرض، فلترة، ترتيب، طباعة، تصدير، تحديث مباشر، واختيار سجلات محددة للطباعة أو التصدير</p>
            </div>

            <div class="d-flex gap-2 flex-wrap align-items-center">
                @can('create merchants')
                    <a href="{{ route('merchants.create') }}" class="btn btn-primary">إضافة تاجر جديد</a>
                @endcan

                @if($canPrint)
                    <button type="button" class="btn btn-outline-dark" id="print-selected-btn">
                        طباعة المحدد
                    </button>
                @endif

                @if($canExport)
                    <button type="button" class="btn btn-outline-success" id="export-selected-btn">
                        تصدير المحدد
                    </button>
                @endif

                @if($canSelect)
                    <button type="button" class="btn btn-outline-warning" id="show-selected-only-btn-top">
                        عرض المختارين فقط
                    </button>

                    <button type="button" class="btn btn-outline-danger" id="clear-selected-btn">
                        إلغاء التحديد
                    </button>

                    <span class="badge bg-dark d-flex align-items-center" id="selected-count-badge">
                    تم اختيار <span id="selected-count" class="mx-1">0</span> تاجر
                </span>
                @endif

                @if($canPrint)
                    <a href="{{ route('merchants.print', request()->query()) }}" target="_blank" class="btn btn-outline-secondary">
                        طباعة الكل حسب الفلتر
                    </a>
                @endif

                @if($canExport)
                    <a href="{{ route('merchants.export', request()->query()) }}" class="btn btn-outline-secondary">
                        تصدير الكل حسب الفلتر
                    </a>
                @endif
            </div>
        </div>

        @if($canPrint)
            <form id="print-selected-form" method="GET" action="{{ route('merchants.print') }}" target="_blank">
                @foreach(request()->except('page', 'selected_ids') as $key => $value)
                    @if(is_array($value))
                        @foreach($value as $item)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <div id="print-selected-inputs"></div>
            </form>
        @endif

        @if($canExport)
            <form id="export-selected-form" method="GET" action="{{ route('merchants.export') }}">
                @foreach(request()->except('page', 'selected_ids') as $key => $value)
                    @if(is_array($value))
                        @foreach($value as $item)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <div id="export-selected-inputs"></div>
            </form>
        @endif

        @php
            $selectedColumns = request('columns', $columns ?? []);
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

            $selectedPrintColumns = request('print_columns', $printColumns ?? $selectedColumns);
        @endphp

        <form method="GET" action="{{ route('merchants.index') }}">
            <div class="accordion mb-4" id="merchantToolsAccordion">

                <div class="accordion-item">
                    <h2 class="accordion-header" id="filtersHeading">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="true" aria-controls="filtersCollapse">
                            الفلاتر والبحث
                        </button>
                    </h2>
                    <div id="filtersCollapse" class="accordion-collapse collapse show" aria-labelledby="filtersHeading" data-bs-parent="#merchantToolsAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="filter-label">بحث عام</label>
                                    <input type="text" name="global" value="{{ request('global') }}" class="form-control" placeholder="اسم، هاتف، إيميل، سجل، قطاع، أعضاء...">
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-label">رقم العضوية</label>
                                    <input type="text" name="membership_no" value="{{ request('membership_no') }}" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-label">اسم الشركة</label>
                                    <input type="text" name="organization_name" value="{{ request('organization_name') }}" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-label">الاسم التجاري</label>
                                    <input type="text" name="commercial_name" value="{{ request('commercial_name') }}" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-label">رقم السجل التجاري</label>
                                    <input type="text" name="commercial_reg_no" value="{{ request('commercial_reg_no') }}" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-label">الرقم الوطني</label>
                                    <input type="text" name="org_national_no" value="{{ request('org_national_no') }}" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-label">القطاع</label>
                                    <input type="text" name="sector" value="{{ request('sector') }}" class="form-control" placeholder="اكتب جزء من اسم القطاع">
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-label">العنوان</label>
                                    <input type="text" name="street" value="{{ request('street') }}" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-label">الوصف</label>
                                    <input type="text" name="description" value="{{ request('description') }}" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-label">المفوّض بالتوقيع</label>
                                    <input type="text" name="delegate" value="{{ request('delegate') }}" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-label">الأعضاء / الشركاء</label>
                                    <input type="text" name="members" value="{{ request('members') }}" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-label">ملاحظات</label>
                                    <input type="text" name="notes" value="{{ request('notes') }}" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label class="filter-label">الهاتف</label>
                                    <input type="text" name="phone" value="{{ request('phone') }}" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label class="filter-label">الموبايل</label>
                                    <input type="text" name="mobile" value="{{ request('mobile') }}" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label class="filter-label">الايميل</label>
                                    <input type="text" name="email" value="{{ request('email') }}" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label class="filter-label">الفاكس</label>
                                    <input type="text" name="fax" value="{{ request('fax') }}" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label class="filter-label">صندوق البريد</label>
                                    <input type="text" name="po_box" value="{{ request('po_box') }}" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label class="filter-label">وصف الرمز البريدي</label>
                                    <input type="text" name="zipcode_desc" value="{{ request('zipcode_desc') }}" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label class="filter-label">الرمز البريدي</label>
                                    <input type="text" name="zipcode" value="{{ request('zipcode') }}" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label class="filter-label">تاريخ التسجيل</label>
                                    <input type="date" name="registered_date" value="{{ request('registered_date') }}" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label class="filter-label">تاريخ السجل التجاري</label>
                                    <input type="date" name="commercial_reg_date" value="{{ request('commercial_reg_date') }}" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label class="filter-label">تم التواصل</label>
                                    <select name="contacted" class="form-select">
                                        <option value="">الكل</option>
                                        <option value="1" {{ request('contacted') == '1' ? 'selected' : '' }}>نعم</option>
                                        <option value="0" {{ request('contacted') == '0' ? 'selected' : '' }}>لا</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="filter-label">الدعوة</label>
                                    <select name="invited" class="form-select">
                                        <option value="">الكل</option>
                                        <option value="1" {{ request('invited') == '1' ? 'selected' : '' }}>نعم</option>
                                        <option value="0" {{ request('invited') == '0' ? 'selected' : '' }}>لا</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3 d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-success">تطبيق الفلاتر</button>
                                <a href="{{ route('merchants.index') }}" class="btn btn-secondary">إعادة ضبط</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="columnsHeading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#columnsCollapse" aria-expanded="false" aria-controls="columnsCollapse">
                            اختيار الأعمدة المعروضة في الجدول
                        </button>
                    </h2>
                    <div id="columnsCollapse" class="accordion-collapse collapse" aria-labelledby="columnsHeading" data-bs-parent="#merchantToolsAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                @foreach($columnLabels as $key => $label)
                                    <div class="col-md-3 col-sm-4 col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="columns[]" value="{{ $key }}" id="col_{{ $key }}"
                                                {{ in_array($key, $selectedColumns) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="col_{{ $key }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-success">حفظ اختيار الأعمدة</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="printColumnsHeading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#printColumnsCollapse" aria-expanded="false" aria-controls="printColumnsCollapse">
                            اختيار الأعمدة الظاهرة في الطباعة والتصدير
                        </button>
                    </h2>
                    <div id="printColumnsCollapse" class="accordion-collapse collapse" aria-labelledby="printColumnsHeading" data-bs-parent="#merchantToolsAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                @foreach($columnLabels as $key => $label)
                                    <div class="col-md-3 col-sm-4 col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="print_columns[]" value="{{ $key }}" id="print_col_{{ $key }}"
                                                {{ in_array($key, $selectedPrintColumns) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="print_col_{{ $key }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-success">حفظ أعمدة الطباعة والتصدير</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>

        @if($canSelect)
            <div class="card mb-3 selected-preview-card" id="selected-preview-box" style="display: none;">
                <div class="card-body d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <div class="fw-bold mb-2">
                            المختارون:
                            <span id="selected-preview-count">0</span>
                        </div>

                        <div id="selected-preview-names" class="text-muted small">
                            لا يوجد عناصر محددة
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="show-selected-only-btn">
                            عرض المختارين فقط
                        </button>

                        <button type="button" class="btn btn-sm btn-outline-danger" id="clear-selected-btn-2">
                            إلغاء التحديد
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between bg-white">
                <span>النتائج</span>
                <span class="badge bg-primary small-badge">{{ $merchants->total() }} سجل</span>
            </div>

            <div class="card-body">
                @php
                    function sort_link($label, $column, $sort, $direction) {
                        $dir = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
                        $icon = ($sort === $column) ? ($direction === 'asc' ? ' ↑' : ' ↓') : '';
                        $query = array_merge(request()->query(), ['sort' => $column, 'direction' => $dir]);
                        return '<a class="sortable-link" href="' . route('merchants.index', $query) . '">' . $label . $icon . '</a>';
                    }
                @endphp

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                        <tr>
                            @if($canSelect)
                                <th class="row-selector-cell">
                                    <input type="checkbox" id="select-all-rows">
                                </th>
                            @endif

                            @if(in_array('id', $columns)) <th>{!! sort_link('#', 'id', $sort, $direction) !!}</th> @endif
                            @if(in_array('membership_no', $columns)) <th>{!! sort_link('رقم العضوية', 'membership_no', $sort, $direction) !!}</th> @endif
                            @if(in_array('organization_name', $columns)) <th>{!! sort_link('اسم الشركة', 'organization_name', $sort, $direction) !!}</th> @endif
                            @if(in_array('commercial_name', $columns)) <th>{!! sort_link('الاسم التجاري', 'commercial_name', $sort, $direction) !!}</th> @endif
                            @if(in_array('commercial_reg_no', $columns)) <th>{!! sort_link('رقم السجل التجاري', 'commercial_reg_no', $sort, $direction) !!}</th> @endif
                            @if(in_array('org_national_no', $columns)) <th>{!! sort_link('الرقم الوطني', 'org_national_no', $sort, $direction) !!}</th> @endif
                            @if(in_array('sector', $columns)) <th>{!! sort_link('القطاع', 'sector', $sort, $direction) !!}</th> @endif
                            @if(in_array('street', $columns)) <th>{!! sort_link('العنوان', 'street', $sort, $direction) !!}</th> @endif
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
                            @if(in_array('registered_date', $columns)) <th>{!! sort_link('تاريخ التسجيل', 'registered_date', $sort, $direction) !!}</th> @endif
                            @if(in_array('commercial_reg_date', $columns)) <th>{!! sort_link('تاريخ السجل التجاري', 'commercial_reg_date', $sort, $direction) !!}</th> @endif
                            @if(in_array('contacted', $columns)) <th>{!! sort_link('تم التواصل', 'contacted', $sort, $direction) !!}</th> @endif
                            @if(in_array('invited', $columns)) <th>{!! sort_link('الدعوة', 'invited', $sort, $direction) !!}</th> @endif
                            @if(in_array('notes', $columns)) <th>ملاحظات</th> @endif

                            <th>عرض</th>

                            @if($showActions)
                                <th>إجراءات</th>
                            @endif
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($merchants as $m)
                            <tr id="merchant-row-{{ $m->id }}">
                                @if($canSelect)
                                    <td class="row-selector-cell">
                                        <input
                                            type="checkbox"
                                            class="row-selector"
                                            value="{{ $m->id }}"
                                            data-name="{{ $m->organization_name }}"
                                        >
                                    </td>
                                @endif

                                @if(in_array('id', $columns)) <td>{{ $m->id }}</td> @endif
                                @if(in_array('membership_no', $columns)) <td>{{ $m->membership_no }}</td> @endif
                                @if(in_array('organization_name', $columns)) <td>{{ $m->organization_name }}</td> @endif
                                @if(in_array('commercial_name', $columns)) <td>{{ $m->commercial_name }}</td> @endif
                                @if(in_array('commercial_reg_no', $columns)) <td>{{ $m->commercial_reg_no }}</td> @endif
                                @if(in_array('org_national_no', $columns)) <td>{{ $m->org_national_no }}</td> @endif
                                @if(in_array('sector', $columns)) <td>{{ $m->sector }}</td> @endif
                                @if(in_array('street', $columns)) <td>{{ $m->street }}</td> @endif
                                @if(in_array('description', $columns)) <td>{{ $m->description }}</td> @endif
                                @if(in_array('delegate', $columns)) <td>{{ $m->delegate_to_sign_on_management }}</td> @endif
                                @if(in_array('members', $columns)) <td>{{ $m->members }}</td> @endif
                                @if(in_array('po_box', $columns)) <td>{{ $m->po_box }}</td> @endif
                                @if(in_array('zipcode_desc', $columns)) <td>{{ $m->zipcode_desc }}</td> @endif
                                @if(in_array('zipcode', $columns)) <td>{{ $m->zipcode }}</td> @endif

                                @if(in_array('phones', $columns))
                                    <td>
                                        @forelse($m->phones as $p)
                                            <div>{{ $p->phone }}</div>
                                        @empty
                                            <span class="text-muted">-</span>
                                        @endforelse
                                    </td>
                                @endif

                                @if(in_array('mobiles', $columns))
                                    <td>
                                        @forelse($m->mobiles as $p)
                                            <div>{{ $p->mobile }}</div>
                                        @empty
                                            <span class="text-muted">-</span>
                                        @endforelse
                                    </td>
                                @endif

                                @if(in_array('emails', $columns))
                                    <td>
                                        @forelse($m->emails as $p)
                                            <div>{{ $p->email }}</div>
                                        @empty
                                            <span class="text-muted">-</span>
                                        @endforelse
                                    </td>
                                @endif

                                @if(in_array('faxes', $columns))
                                    <td>
                                        @forelse($m->faxes as $p)
                                            <div>{{ $p->fax }}</div>
                                        @empty
                                            <span class="text-muted">-</span>
                                        @endforelse
                                    </td>
                                @endif

                                @if(in_array('registered_date', $columns))
                                    <td>{{ optional($m->registered_date)->format('Y-m-d') }}</td>
                                @endif

                                @if(in_array('commercial_reg_date', $columns))
                                    <td>{{ optional($m->commercial_reg_date)->format('Y-m-d') }}</td>
                                @endif

                                @if(in_array('contacted', $columns))
                                    <td>
                                        @if($canInlineEdit)
                                            <input
                                                type="checkbox"
                                                class="form-check-input inline-contacted"
                                                data-id="{{ $m->id }}"
                                                {{ $m->contacted ? 'checked' : '' }}
                                            >
                                        @else
                                            {{ $m->contacted ? 'نعم' : 'لا' }}
                                        @endif
                                    </td>
                                @endif

                                @if(in_array('invited', $columns))
                                    <td>
                                        @if($canInlineEdit)
                                            <input
                                                type="checkbox"
                                                class="form-check-input inline-invited"
                                                data-id="{{ $m->id }}"
                                                {{ $m->invited ? 'checked' : '' }}
                                            >
                                        @else
                                            {{ $m->invited ? 'نعم' : 'لا' }}
                                        @endif
                                    </td>
                                @endif

                                @if(in_array('notes', $columns))
                                    <td>
                                        @if($canInlineEdit)
                                            <textarea
                                                class="form-control form-control-sm inline-notes"
                                                data-id="{{ $m->id }}"
                                                rows="2"
                                            >{{ $m->notes }}</textarea>
                                        @else
                                            {{ $m->notes ?: '-' }}
                                        @endif
                                    </td>
                                @endif

                                <td>
                                    <a href="{{ route('merchants.show', $m->id) }}" class="btn btn-sm btn-outline-info">عرض</a>
                                </td>

                                @if($showActions)
                                    <td class="actions-cell">
                                        @if($canEdit)
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-success inline-save-btn"
                                                data-id="{{ $m->id }}"
                                            >
                                                حفظ
                                            </button>

                                            <a href="{{ route('merchants.edit', $m->id) }}" class="btn btn-sm btn-outline-primary">تعديل</a>
                                        @endif

                                        @if($canDelete)
                                            <form action="{{ route('merchants.destroy', $m->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                    حذف
                                                </button>
                                            </form>
                                        @endif

                                        <div class="small text-success mt-1 d-none inline-status-msg" id="status-msg-{{ $m->id }}"></div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ ($canSelect ? 1 : 0) + count($columns) + 1 + ($showActions ? 1 : 0) }}" class="text-center text-muted">
                                    لا توجد نتائج
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $merchants->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const token = '{{ csrf_token() }}';
            const STORAGE_KEY = 'selected_merchants_ids';
            const STORAGE_MAP_KEY = 'selected_merchants_map';

            function getStoredSelections() {
                try {
                    return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
                } catch (e) {
                    return [];
                }
            }

            function setStoredSelections(ids) {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
            }

            function getStoredSelectionMap() {
                try {
                    return JSON.parse(localStorage.getItem(STORAGE_MAP_KEY)) || {};
                } catch (e) {
                    return {};
                }
            }

            function setStoredSelectionMap(map) {
                localStorage.setItem(STORAGE_MAP_KEY, JSON.stringify(map));
            }

            function updateSelectedCount() {
                const countElement = document.getElementById('selected-count');
                if (!countElement) return;

                const ids = getStoredSelections();
                countElement.textContent = ids.length;
            }

            function updateSelectedPreview() {
                const ids = getStoredSelections();
                const map = getStoredSelectionMap();

                const box = document.getElementById('selected-preview-box');
                const count = document.getElementById('selected-preview-count');
                const names = document.getElementById('selected-preview-names');

                if (!box || !count || !names) return;

                count.textContent = ids.length;

                if (ids.length === 0) {
                    box.style.display = 'none';
                    names.innerHTML = 'لا يوجد عناصر محددة';
                    return;
                }

                box.style.display = 'block';

                const selectedNames = ids.map(id => map[id] || ('#' + id));
                const previewNames = selectedNames.slice(0, 10);

                let html = previewNames.map(name => `<span class="selected-name-badge">${name}</span>`).join(' ');

                if (selectedNames.length > 10) {
                    html += `<div class="mt-2 text-muted">و ${selectedNames.length - 10} عنصر إضافي...</div>`;
                }

                names.innerHTML = html;
            }

            function addSelection(id, name) {
                let ids = getStoredSelections();
                let map = getStoredSelectionMap();

                id = String(id);

                if (!ids.includes(id)) {
                    ids.push(id);
                }

                map[id] = name || ('#' + id);

                setStoredSelections(ids);
                setStoredSelectionMap(map);

                updateSelectedCount();
                updateSelectedPreview();
            }

            function removeSelection(id) {
                let ids = getStoredSelections();
                let map = getStoredSelectionMap();

                id = String(id);

                ids = ids.filter(item => item !== id);
                delete map[id];

                setStoredSelections(ids);
                setStoredSelectionMap(map);

                updateSelectedCount();
                updateSelectedPreview();
            }

            function syncCheckboxesWithStorage() {
                const ids = getStoredSelections();

                document.querySelectorAll('.row-selector').forEach(cb => {
                    cb.checked = ids.includes(String(cb.value));
                });

                updateSelectAllState();
                updateSelectedCount();
                updateSelectedPreview();
            }

            function updateSelectAllState() {
                const allCheckboxes = Array.from(document.querySelectorAll('.row-selector'));
                const checkedCheckboxes = allCheckboxes.filter(cb => cb.checked);

                const selectAll = document.getElementById('select-all-rows');
                if (!selectAll) return;

                if (allCheckboxes.length === 0) {
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                    return;
                }

                if (checkedCheckboxes.length === 0) {
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                } else if (checkedCheckboxes.length === allCheckboxes.length) {
                    selectAll.checked = true;
                    selectAll.indeterminate = false;
                } else {
                    selectAll.checked = false;
                    selectAll.indeterminate = true;
                }
            }

            function getSelectedIds() {
                return getStoredSelections();
            }

            function fillSelectedInputs(containerId, selectedIds) {
                const container = document.getElementById(containerId);
                if (!container) return;

                container.innerHTML = '';

                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'selected_ids[]';
                    input.value = id;
                    container.appendChild(input);
                });
            }

            document.querySelectorAll('.inline-save-btn').forEach(button => {
                button.addEventListener('click', async function () {
                    const merchantId = this.dataset.id;
                    const row = document.getElementById(`merchant-row-${merchantId}`);

                    const contactedInput = row.querySelector('.inline-contacted');
                    const invitedInput = row.querySelector('.inline-invited');
                    const notesInput = row.querySelector('.inline-notes');
                    const statusMsg = document.getElementById(`status-msg-${merchantId}`);

                    const payload = {
                        contacted: contactedInput ? (contactedInput.checked ? 1 : 0) : 0,
                        invited: invitedInput ? (invitedInput.checked ? 1 : 0) : 0,
                        notes: notesInput ? notesInput.value : ''
                    };

                    this.disabled = true;
                    this.textContent = 'جاري الحفظ...';

                    try {
                        const response = await fetch(`{{ route('merchants.inlineUpdate', ':id') }}`.replace(':id', merchantId), {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw data;
                        }

                        row.classList.add('table-success');

                        if (statusMsg) {
                            statusMsg.textContent = data.message || 'تم الحفظ';
                            statusMsg.classList.remove('d-none');
                        }

                        setTimeout(() => {
                            row.classList.remove('table-success');
                            if (statusMsg) {
                                statusMsg.classList.add('d-none');
                            }
                        }, 1500);

                    } catch (error) {
                        alert('حدث خطأ أثناء الحفظ');
                        console.error(error);
                    } finally {
                        this.disabled = false;
                        this.textContent = 'حفظ';
                    }
                });
            });

            document.querySelectorAll('.row-selector').forEach(cb => {
                cb.addEventListener('change', function () {
                    const name = this.dataset.name || ('#' + this.value);

                    if (this.checked) {
                        addSelection(this.value, name);
                    } else {
                        removeSelection(this.value);
                    }

                    updateSelectAllState();
                });
            });

            const selectAll = document.getElementById('select-all-rows');
            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    document.querySelectorAll('.row-selector').forEach(cb => {
                        cb.checked = this.checked;

                        const name = cb.dataset.name || ('#' + cb.value);

                        if (this.checked) {
                            addSelection(cb.value, name);
                        } else {
                            removeSelection(cb.value);
                        }
                    });

                    updateSelectAllState();
                });
            }

            syncCheckboxesWithStorage();

            const printBtn = document.getElementById('print-selected-btn');
            const exportBtn = document.getElementById('export-selected-btn');

            if (printBtn) {
                printBtn.addEventListener('click', function () {
                    const selectedIds = getSelectedIds();

                    if (selectedIds.length === 0) {
                        alert('اختر تاجرًا واحدًا على الأقل للطباعة');
                        return;
                    }

                    fillSelectedInputs('print-selected-inputs', selectedIds);
                    document.getElementById('print-selected-form')?.submit();
                });
            }

            if (exportBtn) {
                exportBtn.addEventListener('click', function () {
                    const selectedIds = getSelectedIds();

                    if (selectedIds.length === 0) {
                        alert('اختر تاجرًا واحدًا على الأقل للتصدير');
                        return;
                    }

                    fillSelectedInputs('export-selected-inputs', selectedIds);
                    document.getElementById('export-selected-form')?.submit();
                });
            }

            function showSelectedOnly() {
                const selectedIds = getSelectedIds();

                if (selectedIds.length === 0) {
                    alert('لا يوجد تجار محددون');
                    return;
                }

                const url = new URL(window.location.href);
                url.searchParams.delete('page');
                url.searchParams.delete('selected_ids[]');
                selectedIds.forEach(id => url.searchParams.append('selected_ids[]', id));
                window.location.href = url.toString();
            }

            const showSelectedOnlyBtn = document.getElementById('show-selected-only-btn');
            const showSelectedOnlyBtnTop = document.getElementById('show-selected-only-btn-top');

            if (showSelectedOnlyBtn) {
                showSelectedOnlyBtn.addEventListener('click', showSelectedOnly);
            }

            if (showSelectedOnlyBtnTop) {
                showSelectedOnlyBtnTop.addEventListener('click', showSelectedOnly);
            }

            function clearAllSelections() {
                localStorage.removeItem(STORAGE_KEY);
                localStorage.removeItem(STORAGE_MAP_KEY);
                syncCheckboxesWithStorage();
                updateSelectedCount();
                updateSelectedPreview();
                alert('تم إلغاء جميع الاختيارات');
            }

            const clearSelectionBtn = document.getElementById('clear-selected-btn');
            const clearSelectionBtn2 = document.getElementById('clear-selected-btn-2');

            if (clearSelectionBtn) {
                clearSelectionBtn.addEventListener('click', clearAllSelections);
            }

            if (clearSelectionBtn2) {
                clearSelectionBtn2.addEventListener('click', clearAllSelections);
            }
        });
    </script>
@endpush
