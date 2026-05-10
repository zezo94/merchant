<div class="card section-card">
    <div class="card-header bg-white">
        <h5 class="section-title">بيانات المستخدم</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ $formAction }}">
            @csrf

            @if($formMethod !== 'POST')
                @method($formMethod)
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">الاسم</label>
                    <input
                        type="text"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $userModel?->name) }}"
                        required
                    >
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $userModel?->email) }}"
                        required
                    >
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        {{ $mode === 'edit' ? 'كلمة المرور الجديدة' : 'كلمة المرور' }}
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        {{ $mode === 'create' ? 'required' : '' }}
                    >

                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if($mode === 'edit')
                        <small class="text-muted">اتركها فارغة إذا لا تريد تغييرها</small>
                    @endif
                </div>

                <div class="col-md-6">
                    <label class="form-label">تأكيد كلمة المرور</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-control"
                        {{ $mode === 'create' ? 'required' : '' }}
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label">الدور</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="">اختر الدور</option>
                        @foreach($roles as $role)
                            <option
                                value="{{ $role->name }}"
                                {{ old('role', $userModel?->roles?->first()?->name) === $role->name ? 'selected' : '' }}
                            >
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if($isRoot)
                        <small class="text-muted">
                            بما أنك Root يمكنك {{ $mode === 'create' ? 'إنشاء' : 'تعيين' }} أي role متاح في القائمة
                        </small>
                    @else
                        <small class="text-muted">المتاح فقط: admin أو user</small>
                    @endif
                </div>
            </div>

            <div class="mt-4 d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-success">{{ $submitLabel }}</button>
                <a href="{{ $cancelRoute }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
