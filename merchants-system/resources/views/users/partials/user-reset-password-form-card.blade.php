<div class="card section-card">
    <div class="card-header bg-white">
        <h5 class="section-title">كلمة المرور الجديدة</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('users.reset-password', $user->id) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">كلمة المرور الجديدة</label>
                    <input
                        type="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        required
                    >
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">تأكيد كلمة المرور</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-control"
                        required
                    >
                </div>
            </div>

            <div class="mt-4 d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-success">حفظ كلمة المرور الجديدة</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
