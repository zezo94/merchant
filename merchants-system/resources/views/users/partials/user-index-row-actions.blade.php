@if($canManageTarget)
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">
            تعديل
        </a>

        <a href="{{ route('users.reset-password.form', $user->id) }}" class="btn btn-sm btn-outline-warning">
            Reset Password
        </a>

        <form method="POST" action="{{ route('users.destroy', $user->id) }}" class="d-inline">
            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="btn btn-sm btn-outline-danger"
                onclick="return confirm('هل أنت متأكد من حذف المستخدم؟')"
            >
                حذف
            </button>
        </form>
    </div>
@else
    @if($isTargetRoot)
        <span class="badge bg-danger">Root محمي</span>
    @else
        <span class="badge bg-dark">Super Admin محمي</span>
    @endif
@endif
