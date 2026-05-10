<div class="card section-card mb-4">
    <div class="card-header bg-white">
        <h5 class="section-title">الفلاتر والبحث</h5>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('users.index') }}">
            <div class="row g-3">
                <div class="col-md-5">
                    <label for="search" class="form-label filter-label">بحث</label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        class="form-control"
                        value="{{ request('search') }}"
                        placeholder="الاسم أو البريد الإلكتروني"
                    >
                </div>

                <div class="col-md-4">
                    <label for="role" class="form-label filter-label">الدور</label>
                    <select id="role" name="role" class="form-select">
                        <option value="">الكل</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-success w-100">تطبيق</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary w-100">إعادة ضبط</a>
                </div>
            </div>
        </form>
    </div>
</div>
