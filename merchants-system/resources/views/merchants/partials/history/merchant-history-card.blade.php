<div class="card section-card">
    <div class="card-header bg-white">
        <h5 class="section-title">سجل التعديلات على هذا التاجر</h5>
    </div>

    <div class="card-body">
        @if(isset($merchantTimeline) && $merchantTimeline->count())
            <div class="timeline-box">
                <div class="accordion" id="merchantHistoryAccordion">
                    @foreach($merchantTimeline as $index => $log)
                        @include('merchants.partials.history.merchant-history-timeline-item', [
                            'log' => $log,
                            'index' => $index,
                            'fieldLabels' => $fieldLabels,
                            'actionTitles' => $actionTitles,
                            'actionColors' => $actionColors,
                            'formatValue' => $formatValue,
                        ])
                    @endforeach
                </div>
            </div>
        @else
            <div class="alert alert-secondary mb-0">
                لا يوجد سجل تعديلات لهذا التاجر حتى الآن.
            </div>
        @endif
    </div>
</div>
