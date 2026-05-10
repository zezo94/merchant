<div class="{{ $title ? 'result-box' : '' }}">
    @if($title)
        <div class="result-title {{ $titleClass }}">{{ $title }}</div>
    @endif

    @if(count($changes))
        <div class="table-responsive">
            <table class="table table-sm table-bordered history-table {{ $title ? 'mb-0' : '' }}">
                <thead class="table-light">
                <tr>
                    <th>الحقل</th>

                    @if($valueType === 'old')
                        <th>القيمة القديمة</th>
                    @elseif($valueType === 'new')
                        <th>القيمة الجديدة</th>
                    @else
                        <th>القيمة القديمة</th>
                        <th>القيمة الجديدة</th>
                    @endif
                </tr>
                </thead>

                <tbody>
                @foreach($changes as $change)
                    @include('merchants.partials.history.merchant-history-change-row', [
                        'change' => $change,
                        'valueType' => $valueType,
                    ])
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        @if($title)
            <div class="text-muted">{{ $emptyText }}</div>
        @else
            <div class="table-responsive">
                <table class="table table-sm table-bordered history-table">
                    <tbody>
                    <tr>
                        <td colspan="{{ $emptyColumns }}" class="text-center text-muted">
                            {{ $emptyText }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        @endif
    @endif
</div>
