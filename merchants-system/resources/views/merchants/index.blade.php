@extends('layouts.app')

@php
    $pageTitle = 'إدارة التجار';

    $canPrint = auth()->user()?->can('print merchants');
    $canExport = auth()->user()?->can('export merchants');
    $canSelect = $canPrint || $canExport;
    $canEdit = auth()->user()?->can('edit merchants');
    $canDelete = auth()->user()?->can('delete merchants');
    $canInlineEdit = $canEdit;
    $showActions = $canEdit || $canDelete;

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

    $selectedColumns = request('columns', $columns ?? array_keys($columnLabels));
    $selectedPrintColumns = request('print_columns', $printColumns ?? $selectedColumns);
@endphp

@section('body_class', 'app-body merchants-index-page')

@section('content')
    <section
        class="merchants-index-section"
        data-can-select="{{ $canSelect ? '1' : '0' }}"
        data-can-inline-edit="{{ $canInlineEdit ? '1' : '0' }}"
        data-inline-update-url-template="{{ route('merchants.inlineUpdate', ':id') }}"
        data-csrf-token="{{ csrf_token() }}"
    >
        <div class="merchants-page-header mb-4">
            <div class="merchants-page-heading">
                <h2 class="mb-1">إدارة التجار</h2>
                <p class="text-muted mb-0">الفلاتر، اختيار الأعمدة، الطباعة، التصدير، وإدارة السجلات</p>
            </div>

            <div class="merchants-top-actions">
                @can('create merchants')
                    <a href="{{ route('merchants.create') }}" class="btn btn-primary merchants-top-action-btn">
                        إضافة تاجر جديد
                    </a>
                @endcan

                @if($canPrint)
                    <button type="button" class="btn btn-outline-dark merchants-top-action-btn" id="print-selected-btn">
                        طباعة المحدد
                    </button>
                @endif

                @if($canExport)
                    <button type="button" class="btn btn-outline-success merchants-top-action-btn" id="export-selected-btn">
                        تصدير المحدد
                    </button>
                @endif

                @if($canPrint)
                    <button type="button" class="btn btn-outline-secondary merchants-top-action-btn" id="print-all-filtered-btn">
                        طباعة الكل حسب الفلتر
                    </button>
                @endif

                @if($canExport)
                    <button type="button" class="btn btn-outline-secondary merchants-top-action-btn" id="export-all-filtered-btn">
                        تصدير الكل حسب الفلتر
                    </button>
                @endif

                @if($canSelect)
                    <button type="button" class="btn btn-outline-warning merchants-top-action-btn" id="show-selected-only-btn-top">
                        عرض المختارين فقط
                    </button>

                    <button type="button" class="btn btn-outline-danger merchants-top-action-btn" id="clear-selected-btn">
                        إلغاء التحديد
                    </button>

                    <div class="badge bg-dark merchants-selected-count-badge" id="selected-count-badge">
                        تم اختيار <span id="selected-count" class="mx-1">0</span> تاجر
                    </div>
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

            <form id="print-all-filtered-form" method="GET" action="{{ route('merchants.print') }}" target="_blank">
                @foreach(request()->except('page', 'selected_ids') as $key => $value)
                    @if(is_array($value))
                        @foreach($value as $item)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
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

            <form id="export-all-filtered-form" method="GET" action="{{ route('merchants.export') }}">
                @foreach(request()->except('page', 'selected_ids') as $key => $value)
                    @if(is_array($value))
                        @foreach($value as $item)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
            </form>
        @endif

        @include('merchants.partials.merchant-index-tools-card', [
            'columnLabels' => $columnLabels,
            'selectedColumns' => $selectedColumns,
            'selectedPrintColumns' => $selectedPrintColumns,
        ])

        @if($canSelect)
            @include('merchants.partials.merchant-index-selected-preview-card')
        @endif

        @include('merchants.partials.merchant-index-table', [
            'merchants' => $merchants,
            'columns' => $selectedColumns,
            'sort' => $sort ?? 'id',
            'direction' => $direction ?? 'desc',
            'canSelect' => $canSelect,
            'canInlineEdit' => $canInlineEdit,
            'showActions' => $showActions,
            'canEdit' => $canEdit,
            'canDelete' => $canDelete,
        ])


    </section>


@endsection



