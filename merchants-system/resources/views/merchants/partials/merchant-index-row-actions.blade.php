<div class="merchants-actions-stack">
    @if($canEdit)
        <button
            type="button"
            class="btn btn-sm btn-success inline-save-btn merchants-action-btn"
            data-id="{{ $merchant->id }}"
        >
            حفظ
        </button>

        <a
            href="{{ route('merchants.edit', $merchant->id) }}"
            class="btn btn-sm btn-outline-primary merchants-action-btn"
        >
            تعديل
        </a>
    @endif

    @if($canDelete)
        <form action="{{ route('merchants.destroy', $merchant->id) }}" method="POST" class="merchants-action-form">
            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="btn btn-sm btn-outline-danger merchants-action-btn w-100"
                onclick="return confirm('هل أنت متأكد من الحذف؟')"
            >
                حذف
            </button>
        </form>
    @endif

    <div class="small text-success d-none inline-status-msg" id="status-msg-{{ $merchant->id }}"></div>
</div>
