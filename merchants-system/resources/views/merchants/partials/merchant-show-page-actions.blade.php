<div class="d-flex gap-2 flex-wrap">
    @can('edit merchants')
        <a href="{{ route('merchants.edit', $merchant->id) }}" class="btn btn-primary">
            تعديل
        </a>
    @endcan

    <a href="{{ route('merchants.index') }}" class="btn btn-outline-secondary">
        رجوع للقائمة
    </a>

        @can('print merchants')
            <a
                href="{{ route('merchants.show.print', $merchant->id) }}"
                target="_blank"
                class="btn btn-outline-dark"
            >
                طباعة
            </a>
        @endcan

{{--    @can('export merchants')--}}
{{--        <a--}}
{{--            href="{{ route('merchants.show.export', $merchant->id) }}"--}}
{{--            class="btn btn-outline-success"--}}
{{--        >--}}
{{--            تصدير--}}
{{--        </a>--}}
{{--    @endcan--}}

    @can('delete merchants')
        <form action="{{ route('merchants.destroy', $merchant->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="btn btn-outline-danger"
                onclick="return confirm('هل أنت متأكد من حذف هذا السجل؟')"
            >
                حذف
            </button>
        </form>
    @endcan
</div>
