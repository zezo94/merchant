<div class="card section-card mb-4">
    <div class="card-header bg-white">
        <h5 class="section-title">الفلاتر والبحث</h5>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('merchants.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label filter-label">بحث عام</label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        class="form-control"
                        value="{{ request('search') }}"
                        placeholder="اسم الشركة / الاسم التجاري / رقم العضوية"
                    >
                </div>

                <div class="col-md-2">
                    <label for="membership_no" class="form-label filter-label">رقم العضوية</label>
                    <input
                        type="text"
                        id="membership_no"
                        name="membership_no"
                        class="form-control"
                        value="{{ request('membership_no') }}"
                    >
                </div>

                <div class="col-md-2">
                    <label for="commercial_reg_no" class="form-label filter-label">رقم السجل التجاري</label>
                    <input
                        type="text"
                        id="commercial_reg_no"
                        name="commercial_reg_no"
                        class="form-control"
                        value="{{ request('commercial_reg_no') }}"
                    >
                </div>

                <div class="col-md-2">
                    <label for="sector" class="form-label filter-label">القطاع</label>
                    <input
                        type="text"
                        id="sector"
                        name="sector"
                        class="form-control"
                        value="{{ request('sector') }}"
                    >
                </div>

                <div class="col-md-1">
                    <label for="contacted" class="form-label filter-label">تم التواصل</label>
                    <select id="contacted" name="contacted" class="form-select">
                        <option value="">الكل</option>
                        <option value="1" {{ request('contacted') === '1' ? 'selected' : '' }}>نعم</option>
                        <option value="0" {{ request('contacted') === '0' ? 'selected' : '' }}>لا</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <label for="invited" class="form-label filter-label">تمت الدعوة</label>
                    <select id="invited" name="invited" class="form-select">
                        <option value="">الكل</option>
                        <option value="1" {{ request('invited') === '1' ? 'selected' : '' }}>نعم</option>
                        <option value="0" {{ request('invited') === '0' ? 'selected' : '' }}>لا</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <label for="per_page" class="form-label filter-label">لكل صفحة</label>
                    <select id="per_page" name="per_page" class="form-select">
                        @foreach([10, 25, 50, 100] as $size)
                            <option value="{{ $size }}" {{ (string) request('per_page', 25) === (string) $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap mt-3">
                <button type="submit" class="btn btn-success">تطبيق</button>
                <a href="{{ route('merchants.index') }}" class="btn btn-secondary">إعادة ضبط</a>
            </div>
        </form>
    </div>
</div>
