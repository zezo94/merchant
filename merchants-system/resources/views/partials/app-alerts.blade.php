@if (session('success') || session('error') || $errors->any())
    <div class="app-alerts mb-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="fw-semibold mb-1">تمت العملية بنجاح</div>
                <div>{{ session('success') }}</div>

                <button type="button"
                        class="btn-close ms-0 me-auto"
                        data-bs-dismiss="alert"
                        aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="fw-semibold mb-1">حدث خطأ</div>
                <div>{{ session('error') }}</div>

                <button type="button"
                        class="btn-close ms-0 me-auto"
                        data-bs-dismiss="alert"
                        aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="fw-semibold mb-2">يرجى مراجعة الأخطاء التالية:</div>

                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

                <button type="button"
                        class="btn-close ms-0 me-auto"
                        data-bs-dismiss="alert"
                        aria-label="Close"></button>
            </div>
        @endif
    </div>
@endif
