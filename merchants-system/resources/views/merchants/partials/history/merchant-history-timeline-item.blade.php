@php
    $title = $actionTitles[$log->action] ?? $log->action;
    $color = $actionColors[$log->action] ?? 'dark';
    $old = is_array($log->old_values) ? $log->old_values : [];
    $new = is_array($log->new_values) ? $log->new_values : [];
    $keys = array_unique(array_merge(array_keys($old), array_keys($new)));
    $collapseId = 'historyCollapse' . $index;
    $headingId = 'historyHeading' . $index;

    $changes = [];

    foreach ($keys as $key) {
        $oldValue = $formatValue($old[$key] ?? null);
        $newValue = $formatValue($new[$key] ?? null);

        if ($oldValue != $newValue) {
            $changes[] = [
                'key' => $key,
                'label' => $fieldLabels[$key] ?? $key,
                'old' => $oldValue,
                'new' => $newValue,
            ];
        }
    }
@endphp

<div class="timeline-item">
    <button
        class="timeline-header-btn"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#{{ $collapseId }}"
        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
        aria-controls="{{ $collapseId }}"
    >
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <div class="timeline-title">{{ $title }}</div>
                <div class="timeline-meta">
                    بواسطة:
                    <strong>{{ $log->user?->name ?? '-' }}</strong>
                </div>
            </div>

            <div class="text-end">
                <span class="badge bg-{{ $color }}">{{ $log->action }}</span>
                <div class="timeline-meta mt-1">{{ $log->created_at }}</div>
            </div>
        </div>
    </button>

    <div
        id="{{ $collapseId }}"
        class="collapse {{ $index === 0 ? 'show' : '' }}"
        aria-labelledby="{{ $headingId }}"
        data-bs-parent="#merchantHistoryAccordion"
    >
        <div class="px-3 pb-3">
            @if($log->description)
                <div class="mb-3">
                    <div class="timeline-section-label">وصف العملية</div>
                    <div class="info-value">{{ $log->description }}</div>
                </div>
            @endif

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    @include('merchants.partials.history.merchant-history-edit-log-table', [
                        'title' => 'Old Result',
                        'titleClass' => 'text-danger',
                        'changes' => $changes,
                        'valueType' => 'old',
                        'emptyColumns' => 2,
                        'emptyText' => 'لا يوجد',
                    ])
                </div>

                <div class="col-md-6">
                    @include('merchants.partials.history.merchant-history-edit-log-table', [
                        'title' => 'New Result',
                        'titleClass' => 'text-success',
                        'changes' => $changes,
                        'valueType' => 'new',
                        'emptyColumns' => 2,
                        'emptyText' => 'لا يوجد',
                    ])
                </div>
            </div>

            @if(count($changes))
                <div class="timeline-section-label">تفاصيل التعديل</div>

                @include('merchants.partials.history.merchant-history-edit-log-table', [
                    'title' => null,
                    'titleClass' => null,
                    'changes' => $changes,
                    'valueType' => 'both',
                    'emptyColumns' => 3,
                    'emptyText' => 'لا توجد فروقات تفصيلية',
                ])
            @else
                <div class="info-value">لا توجد بيانات تفصيلية لهذا السجل</div>
            @endif
        </div>
    </div>
</div>
