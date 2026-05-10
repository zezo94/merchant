@php
    use Illuminate\Support\Str;

    $tableColumnLabels = [
        'id' => '#',
        'membership_no' => 'رقم العضوية',
        'organization_name' => 'اسم الشركة',
        'commercial_name' => 'الاسم التجاري',
        'commercial_reg_no' => 'رقم السجل',
        'org_national_no' => 'الرقم الوطني',
        'sector' => 'القطاع',
        'street' => 'العنوان',
        'description' => 'الوصف',
        'delegate' => 'المفوّض',
        'members' => 'الأعضاء',
        'po_box' => 'ص.ب',
        'zipcode_desc' => 'وصف الرمز',
        'zipcode' => 'الرمز',
        'phones' => 'الهاتف',
        'mobiles' => 'الموبايل',
        'emails' => 'الإيميل',
        'faxes' => 'الفاكس',
        'registered_date' => 'تاريخ التسجيل',
        'commercial_reg_date' => 'تاريخ السجل',
        'contacted' => 'التواصل',
        'invited' => 'الدعوة',
        'notes' => 'ملاحظات',
    ];

    $sortableColumns = [
        'id',
        'membership_no',
        'organization_name',
        'commercial_name',
        'commercial_reg_no',
        'org_national_no',
        'sector',
        'street',
        'registered_date',
        'commercial_reg_date',
        'contacted',
        'invited',
    ];

    function merchant_sort_link($label, $column, $sort, $direction) {
        $dir = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
        $icon = ($sort === $column) ? ($direction === 'asc' ? ' ↑' : ' ↓') : '';
        $query = array_merge(request()->query(), ['sort' => $column, 'direction' => $dir]);

        return '<a class="sortable-link" href="' . route('merchants.index', $query) . '">' . $label . $icon . '</a>';
    }
@endphp

<div class="card section-card">
    <div class="card-header d-flex justify-content-between align-items-center bg-white flex-wrap gap-2">
        <span class="fw-bold">النتائج</span>
        <span class="badge bg-primary merchants-small-badge">{{ $merchants->total() }} سجل</span>
    </div>

    <div class="card-body">
        <div class="table-responsive merchants-table-responsive">
            <table class="table table-bordered table-hover align-middle merchants-table">
                <thead class="table-light">
                <tr>
                    @if($canSelect)
                        <th class="cell-select cell-center">
                            <input type="checkbox" id="select-all-rows">
                        </th>
                    @endif

                    @foreach($columns as $col)
                        @php $label = $tableColumnLabels[$col] ?? $col; @endphp
                        <th class="cell-{{ $col }}">
                            {!! in_array($col, $sortableColumns, true)
                                ? merchant_sort_link($label, $col, $sort, $direction)
                                : e($label) !!}
                        </th>
                    @endforeach

                    <th class="cell-show cell-center">عرض</th>

                    @if($showActions)
                        <th class="cell-actions cell-center">إجراءات</th>
                    @endif
                </tr>
                </thead>

                <tbody>
                @forelse($merchants as $merchant)
                    <tr id="merchant-row-{{ $merchant->id }}">
                        @if($canSelect)
                            <td class="cell-select cell-center">
                                <input
                                    type="checkbox"
                                    class="row-selector"
                                    value="{{ $merchant->id }}"
                                    data-name="{{ $merchant->organization_name }}"
                                >
                            </td>
                        @endif

                        @foreach($columns as $col)
                            <td class="cell-{{ $col }}">
                                @switch($col)
                                    @case('id')
                                        <div class="cell-nowrap">{{ $merchant->id }}</div>
                                        @break

                                    @case('membership_no')
                                        <div class="cell-nowrap">{{ $merchant->membership_no ?: '-' }}</div>
                                        @break

                                    @case('organization_name')
                                        <div class="cell-scroll">{{ $merchant->organization_name ?: '-' }}</div>
                                        @break

                                    @case('commercial_name')
                                        <div class="cell-scroll">{{ $merchant->commercial_name ?: '-' }}</div>
                                        @break

                                    @case('commercial_reg_no')
                                        <div class="cell-nowrap">{{ $merchant->commercial_reg_no ?: '-' }}</div>
                                        @break

                                    @case('org_national_no')
                                        <div class="cell-nowrap">{{ $merchant->org_national_no ?: '-' }}</div>
                                        @break

                                    @case('sector')
                                        <div class="cell-scroll">{{ $merchant->sector ?: '-' }}</div>
                                        @break

                                    @case('street')
                                        <div class="cell-scroll">{{ $merchant->street ?: '-' }}</div>
                                        @break

                                    @case('description')
                                        <div class="cell-scroll">{{ Str::limit($merchant->description ?: '-', 180) }}</div>
                                        @break

                                    @case('delegate')
                                        <div class="cell-scroll">{{ $merchant->delegate_to_sign_on_management ?: '-' }}</div>
                                        @break

                                    @case('members')
                                        <div class="cell-scroll">{{ Str::limit($merchant->members ?: '-', 220) }}</div>
                                        @break

                                    @case('po_box')
                                        <div class="cell-nowrap">{{ $merchant->po_box ?: '-' }}</div>
                                        @break

                                    @case('zipcode_desc')
                                        <div class="cell-scroll">{{ $merchant->zipcode_desc ?: '-' }}</div>
                                        @break

                                    @case('zipcode')
                                        <div class="cell-nowrap">{{ $merchant->zipcode ?: '-' }}</div>
                                        @break

                                    @case('phones')
                                        <div class="cell-scroll cell-lines">
                                            @forelse($merchant->phones as $p)
                                                <div>{{ $p->phone }}</div>
                                            @empty
                                                <span class="cell-muted-empty">-</span>
                                            @endforelse
                                        </div>
                                        @break

                                    @case('mobiles')
                                        <div class="cell-scroll cell-lines">
                                            @forelse($merchant->mobiles as $p)
                                                <div>{{ $p->mobile }}</div>
                                            @empty
                                                <span class="cell-muted-empty">-</span>
                                            @endforelse
                                        </div>
                                        @break

                                    @case('emails')
                                        <div class="cell-scroll cell-lines">
                                            @forelse($merchant->emails as $p)
                                                <div>{{ $p->email }}</div>
                                            @empty
                                                <span class="cell-muted-empty">-</span>
                                            @endforelse
                                        </div>
                                        @break

                                    @case('faxes')
                                        <div class="cell-scroll cell-lines">
                                            @forelse($merchant->faxes as $p)
                                                <div>{{ $p->fax }}</div>
                                            @empty
                                                <span class="cell-muted-empty">-</span>
                                            @endforelse
                                        </div>
                                        @break

                                    @case('registered_date')
                                        <div class="cell-nowrap">{{ optional($merchant->registered_date)->format('Y-m-d') ?: '-' }}</div>
                                        @break

                                    @case('commercial_reg_date')
                                        <div class="cell-nowrap">{{ optional($merchant->commercial_reg_date)->format('Y-m-d') ?: '-' }}</div>
                                        @break

                                    @case('contacted')
                                        <div class="cell-center">
                                            @if($canInlineEdit)
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input inline-contacted"
                                                    data-id="{{ $merchant->id }}"
                                                    {{ $merchant->contacted ? 'checked' : '' }}
                                                >
                                            @else
                                                {{ $merchant->contacted ? 'نعم' : 'لا' }}
                                            @endif
                                        </div>
                                        @break

                                    @case('invited')
                                        <div class="cell-center">
                                            @if($canInlineEdit)
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input inline-invited"
                                                    data-id="{{ $merchant->id }}"
                                                    {{ $merchant->invited ? 'checked' : '' }}
                                                >
                                            @else
                                                {{ $merchant->invited ? 'نعم' : 'لا' }}
                                            @endif
                                        </div>
                                        @break

                                    @case('notes')
                                        @if($canInlineEdit)
                                            <textarea
                                                class="form-control form-control-sm merchants-inline-notes"
                                                data-id="{{ $merchant->id }}"
                                                rows="2"
                                            >{{ $merchant->notes }}</textarea>
                                        @else
                                            <div class="cell-scroll">{{ Str::limit($merchant->notes ?: '-', 160) }}</div>
                                        @endif
                                        @break

                                    @default
                                        <div class="cell-scroll">{{ data_get($merchant, $col) ?: '-' }}</div>
                                @endswitch
                            </td>
                        @endforeach

                        <td class="cell-show cell-center">
                            <a href="{{ route('merchants.show', $merchant->id) }}" class="btn btn-sm btn-outline-info merchants-table-show-btn">
                                عرض
                            </a>
                        </td>

                        @if($showActions)
                            <td class="cell-actions cell-center">
                                @include('merchants.partials.merchant-index-row-actions', [
                                    'merchant' => $merchant,
                                    'canEdit' => $canEdit,
                                    'canDelete' => $canDelete,
                                ])
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
            {{ $merchants->links('vendor.pagination.merchants-mobile') }}
        </div>
    </div>
</div>
