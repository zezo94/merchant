<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Merchant;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MerchantController extends Controller
{
    public function index(Request $request)
    {
        [$query, $columns, $printColumns, $sort, $direction] = $this->buildMerchantsFilteredQuery($request);

        $perPage = $this->resolvePerPage($request);

        $merchants = $query
            ->with($this->merchantRelationsForList())
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view('merchants.index', compact(
            'merchants',
            'columns',
            'printColumns',
            'sort',
            'direction'
        ));
    }

    public function create()
    {
        return view('merchants.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateMerchant($request);

        $validated['contacted'] = $request->has('contacted');
        $validated['invited'] = $request->has('invited');

        $merchant = Merchant::create($validated);

        $this->syncSimpleRows($merchant, 'phones', $request->input('phones'));
        $this->syncSimpleRows($merchant, 'mobiles', $request->input('mobiles'));
        $this->syncSimpleRows($merchant, 'emails', $request->input('emails'));
        $this->syncSimpleRows($merchant, 'faxes', $request->input('faxes'));

        $merchant->load($this->merchantRelationsForList());

        ActivityLogService::log(
            action: 'merchant_created',
            target: $merchant,
            description: 'تم إنشاء تاجر جديد',
            oldValues: null,
            newValues: $this->extractLoggableMerchantValues($merchant)
        );

        return redirect()
            ->route('merchants.index')
            ->with('success', 'تم إضافة التاجر بنجاح');
    }

    public function show(Merchant $merchant)
    {
        $merchant->load($this->merchantRelationsForList());

        $merchantTimeline = collect();

        if (auth()->user()?->is_root) {
            $merchantTimeline = AuditLog::with('user')
                ->where('target_type', Merchant::class)
                ->where('target_id', $merchant->id)
                ->latest()
                ->get();
        }

        return view('merchants.show', compact('merchant', 'merchantTimeline'));
    }

    public function edit(Merchant $merchant)
    {
        $merchant->load($this->merchantRelationsForList());

        return view('merchants.edit', compact('merchant'));
    }

    public function update(Request $request, Merchant $merchant)
    {
        $validated = $this->validateMerchant($request);

        $validated['contacted'] = $request->has('contacted');
        $validated['invited'] = $request->has('invited');

        $merchant->load($this->merchantRelationsForList());
        $oldValues = $this->extractLoggableMerchantValues($merchant);

        $merchant->update($validated);

        $merchant->phones()->delete();
        $merchant->mobiles()->delete();
        $merchant->emails()->delete();
        $merchant->faxes()->delete();

        $this->syncSimpleRows($merchant, 'phones', $request->input('phones'));
        $this->syncSimpleRows($merchant, 'mobiles', $request->input('mobiles'));
        $this->syncSimpleRows($merchant, 'emails', $request->input('emails'));
        $this->syncSimpleRows($merchant, 'faxes', $request->input('faxes'));

        $merchant->load($this->merchantRelationsForList());

        ActivityLogService::log(
            action: 'merchant_updated',
            target: $merchant,
            description: 'تم تعديل بيانات تاجر',
            oldValues: $oldValues,
            newValues: $this->extractLoggableMerchantValues($merchant)
        );

        return redirect()
            ->route('merchants.index')
            ->with('success', 'تم تعديل بيانات التاجر بنجاح');
    }

    public function destroy(Merchant $merchant)
    {
        $merchant->load($this->merchantRelationsForList());
        $oldValues = $this->extractLoggableMerchantValues($merchant);

        ActivityLogService::log(
            action: 'merchant_deleted',
            target: $merchant,
            description: 'تم حذف تاجر',
            oldValues: $oldValues,
            newValues: null
        );

        $merchant->delete();

        return redirect()
            ->route('merchants.index')
            ->with('success', 'تم حذف التاجر');
    }

    public function print(Request $request)
    {
        [$query, $columns, $printColumns, $sort, $direction] = $this->buildMerchantsFilteredQuery($request);

        $merchants = $query
            ->with($this->merchantRelationsForList())
            ->orderBy($sort, $direction)
            ->get();

        return view('merchants.print', [
            'merchants' => $merchants,
            'columns' => $printColumns,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        [$query, $columns, $printColumns, $sort, $direction] = $this->buildMerchantsFilteredQuery($request);

        $merchants = $query
            ->with($this->merchantRelationsForList())
            ->orderBy($sort, $direction)
            ->get();

        return response()->streamDownload(function () use ($merchants, $printColumns) {
            $handle = fopen('php://output', 'w');

            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, $this->buildCsvHeader($printColumns));

            foreach ($merchants as $merchant) {
                fputcsv($handle, $this->buildCsvRow($merchant, $printColumns));
            }

            fclose($handle);
        }, 'merchants-export.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function inlineUpdate(Request $request, Merchant $merchant)
    {
        $request->validate([
            'contacted' => ['nullable', 'boolean'],
            'invited' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldValues = [
            'contacted' => $merchant->contacted,
            'invited' => $merchant->invited,
            'notes' => $merchant->notes,
        ];

        $merchant->update([
            'contacted' => $request->boolean('contacted'),
            'invited' => $request->boolean('invited'),
            'notes' => $request->input('notes'),
        ]);

        ActivityLogService::log(
            action: 'merchant_inline_updated',
            target: $merchant,
            description: 'تم تحديث سريع لبيانات التاجر',
            oldValues: $oldValues,
            newValues: [
                'contacted' => $merchant->contacted,
                'invited' => $merchant->invited,
                'notes' => $merchant->notes,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث السجل بنجاح',
            'merchant' => [
                'id' => $merchant->id,
                'contacted' => $merchant->contacted,
                'invited' => $merchant->invited,
                'notes' => $merchant->notes,
            ],
        ]);
    }

    private function buildMerchantsFilteredQuery(Request $request): array
    {
        $columns = $this->getSelectedColumns($request);
        $printColumns = $this->getSelectedPrintColumns($request);
        [$sort, $direction] = $this->resolveSorting($request);

        $query = $this->filteredMerchantsQuery($request);

        $selectedIds = $this->getSelectedIds($request);
        if (!empty($selectedIds)) {
            $query->whereIn('id', $selectedIds);
        }

        return [$query, $columns, $printColumns, $sort, $direction];
    }

    private function filteredMerchantsQuery(Request $request)
    {
        $query = Merchant::query();

        if ($request->filled('membership_no')) {
            $query->where('membership_no', 'like', '%' . trim($request->membership_no) . '%');
        }

        if ($request->filled('organization_name')) {
            $query->where('organization_name', 'like', '%' . trim($request->organization_name) . '%');
        }

        if ($request->filled('commercial_name')) {
            $query->where('commercial_name', 'like', '%' . trim($request->commercial_name) . '%');
        }

        if ($request->filled('commercial_reg_no')) {
            $query->where('commercial_reg_no', 'like', '%' . trim($request->commercial_reg_no) . '%');
        }

        if ($request->filled('org_national_no')) {
            $query->where('org_national_no', 'like', '%' . trim($request->org_national_no) . '%');
        }

        if ($request->filled('sector')) {
            $query->where('sector', 'like', '%' . trim($request->sector) . '%');
        }

        if ($request->filled('street')) {
            $query->where('street', 'like', '%' . trim($request->street) . '%');
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . trim($request->description) . '%');
        }

        if ($request->filled('delegate')) {
            $query->where('delegate_to_sign_on_management', 'like', '%' . trim($request->delegate) . '%');
        }

        if ($request->filled('members')) {
            $query->where('members', 'like', '%' . trim($request->members) . '%');
        }

        if ($request->filled('po_box')) {
            $query->where('po_box', 'like', '%' . trim($request->po_box) . '%');
        }

        if ($request->filled('zipcode_desc')) {
            $query->where('zipcode_desc', 'like', '%' . trim($request->zipcode_desc) . '%');
        }

        if ($request->filled('zipcode')) {
            $query->where('zipcode', 'like', '%' . trim($request->zipcode) . '%');
        }

        if ($request->filled('notes')) {
            $query->where('notes', 'like', '%' . trim($request->notes) . '%');
        }

        if ($request->filled('phone')) {
            $query->whereHas('phones', function ($q) use ($request) {
                $q->where('phone', 'like', '%' . trim($request->phone) . '%');
            });
        }

        if ($request->filled('mobile')) {
            $query->whereHas('mobiles', function ($q) use ($request) {
                $q->where('mobile', 'like', '%' . trim($request->mobile) . '%');
            });
        }

        if ($request->filled('email')) {
            $query->whereHas('emails', function ($q) use ($request) {
                $q->where('email', 'like', '%' . trim($request->email) . '%');
            });
        }

        if ($request->filled('fax')) {
            $query->whereHas('faxes', function ($q) use ($request) {
                $q->where('fax', 'like', '%' . trim($request->fax) . '%');
            });
        }

        if ($request->filled('registered_date')) {
            $query->whereDate('registered_date', $request->registered_date);
        }

        if ($request->filled('commercial_reg_date')) {
            $query->whereDate('commercial_reg_date', $request->commercial_reg_date);
        }

        if ($request->contacted !== null && $request->contacted !== '') {
            $query->where('contacted', (int) $request->contacted);
        }

        if ($request->invited !== null && $request->invited !== '') {
            $query->where('invited', (int) $request->invited);
        }

        if ($request->filled('global')) {
            $global = trim($request->global);

            $query->where(function ($q) use ($global) {
                $q->where('membership_no', 'like', "%{$global}%")
                    ->orWhere('organization_name', 'like', "%{$global}%")
                    ->orWhere('commercial_name', 'like', "%{$global}%")
                    ->orWhere('commercial_reg_no', 'like', "%{$global}%")
                    ->orWhere('org_national_no', 'like', "%{$global}%")
                    ->orWhere('sector', 'like', "%{$global}%")
                    ->orWhere('street', 'like', "%{$global}%")
                    ->orWhere('description', 'like', "%{$global}%")
                    ->orWhere('delegate_to_sign_on_management', 'like', "%{$global}%")
                    ->orWhere('members', 'like', "%{$global}%")
                    ->orWhere('po_box', 'like', "%{$global}%")
                    ->orWhere('zipcode_desc', 'like', "%{$global}%")
                    ->orWhere('zipcode', 'like', "%{$global}%")
                    ->orWhere('notes', 'like', "%{$global}%")
                    ->orWhereHas('phones', function ($sub) use ($global) {
                        $sub->where('phone', 'like', "%{$global}%");
                    })
                    ->orWhereHas('mobiles', function ($sub) use ($global) {
                        $sub->where('mobile', 'like', "%{$global}%");
                    })
                    ->orWhereHas('emails', function ($sub) use ($global) {
                        $sub->where('email', 'like', "%{$global}%");
                    })
                    ->orWhereHas('faxes', function ($sub) use ($global) {
                        $sub->where('fax', 'like', "%{$global}%");
                    });
            });
        }

        return $query;
    }

    private function validateMerchant(Request $request): array
    {
        return $request->validate([
            'membership_no' => ['nullable', 'string', 'max:255'],
            'organization_name' => ['required', 'string', 'max:255'],
            'registered_date' => ['nullable', 'date'],
            'sub_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'org_national_no' => ['nullable', 'string', 'max:255'],
            'commercial_reg_no' => ['nullable', 'string', 'max:255'],
            'commercial_reg_date' => ['nullable', 'date'],
            'commercial_name' => ['nullable', 'string', 'max:255'],
            'sector' => ['nullable', 'string', 'max:255'],
            'ccate_id' => ['nullable', 'string', 'max:255'],
            'delegate_to_sign_on_management' => ['nullable', 'string', 'max:255'],
            'members' => ['nullable', 'string'],
            'street' => ['nullable', 'string', 'max:255'],
            'po_box' => ['nullable', 'string', 'max:255'],
            'zipcode_desc' => ['nullable', 'string', 'max:255'],
            'zipcode' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],

            'phones' => ['nullable', 'array'],
            'phones.*' => ['nullable', 'string', 'max:255'],

            'mobiles' => ['nullable', 'array'],
            'mobiles.*' => ['nullable', 'string', 'max:255'],

            'emails' => ['nullable', 'array'],
            'emails.*' => ['nullable', 'email', 'max:255'],

            'faxes' => ['nullable', 'array'],
            'faxes.*' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function syncSimpleRows(Merchant $merchant, string $relation, $values): void
    {
        if (!is_array($values)) {
            return;
        }

        $column = match ($relation) {
            'phones' => 'phone',
            'mobiles' => 'mobile',
            'emails' => 'email',
            'faxes' => 'fax',
            default => null,
        };

        if (!$column) {
            return;
        }

        foreach ($values as $value) {
            $value = trim((string) $value);

            if ($value === '') {
                continue;
            }

            $merchant->{$relation}()->create([
                $column => $value,
            ]);
        }
    }

    private function getAvailableColumns(): array
    {
        return [
            'id',
            'membership_no',
            'organization_name',
            'commercial_name',
            'commercial_reg_no',
            'org_national_no',
            'sector',
            'street',
            'description',
            'delegate',
            'members',
            'po_box',
            'zipcode_desc',
            'zipcode',
            'phones',
            'mobiles',
            'emails',
            'faxes',
            'registered_date',
            'commercial_reg_date',
            'contacted',
            'invited',
            'notes',
        ];
    }

    private function getDefaultColumns(): array
    {
        return [
            'id',
            'membership_no',
            'organization_name',
            'commercial_name',
            'commercial_reg_no',
            'org_national_no',
            'sector',
            'street',
            'members',
            'phones',
            'mobiles',
            'emails',
            'faxes',
            'contacted',
            'invited',
            'notes',
        ];
    }

    private function getSelectedColumns(Request $request): array
    {
        $allowedColumns = $this->getAvailableColumns();
        $columns = $request->input('columns', []);

        if (!is_array($columns) || empty($columns)) {
            return $this->getDefaultColumns();
        }

        $selected = array_values(array_intersect($columns, $allowedColumns));

        return !empty($selected) ? $selected : $this->getDefaultColumns();
    }

    private function getSelectedPrintColumns(Request $request): array
    {
        $allowedColumns = $this->getAvailableColumns();
        $printColumns = $request->input('print_columns', []);

        if (!is_array($printColumns) || empty($printColumns)) {
            return $this->getSelectedColumns($request);
        }

        $selected = array_values(array_intersect($printColumns, $allowedColumns));

        return !empty($selected) ? $selected : $this->getSelectedColumns($request);
    }

    private function buildCsvHeader(array $columns): array
    {
        $map = [
            'id' => '#',
            'membership_no' => 'رقم العضوية',
            'organization_name' => 'اسم الشركة',
            'commercial_name' => 'الاسم التجاري',
            'commercial_reg_no' => 'رقم السجل التجاري',
            'org_national_no' => 'الرقم الوطني',
            'sector' => 'القطاع',
            'street' => 'العنوان',
            'description' => 'الوصف',
            'delegate' => 'المفوّض بالتوقيع',
            'members' => 'الأعضاء / الشركاء',
            'po_box' => 'صندوق البريد',
            'zipcode_desc' => 'وصف الرمز البريدي',
            'zipcode' => 'الرمز البريدي',
            'phones' => 'الهاتف',
            'mobiles' => 'الموبايل',
            'emails' => 'الايميل',
            'faxes' => 'الفاكس',
            'registered_date' => 'تاريخ التسجيل',
            'commercial_reg_date' => 'تاريخ السجل التجاري',
            'contacted' => 'تم التواصل',
            'invited' => 'الدعوة',
            'notes' => 'ملاحظات',
        ];

        $header = [];

        foreach ($columns as $column) {
            $header[] = $map[$column] ?? $column;
        }

        return $header;
    }

    private function buildCsvRow(Merchant $merchant, array $columns): array
    {
        $row = [];

        foreach ($columns as $column) {
            $row[] = match ($column) {
                'id' => $merchant->id,
                'membership_no' => $merchant->membership_no,
                'organization_name' => $merchant->organization_name,
                'commercial_name' => $merchant->commercial_name,
                'commercial_reg_no' => $merchant->commercial_reg_no,
                'org_national_no' => $merchant->org_national_no,
                'sector' => $merchant->sector,
                'street' => $merchant->street,
                'description' => $merchant->description,
                'delegate' => $merchant->delegate_to_sign_on_management,
                'members' => $merchant->members,
                'po_box' => $merchant->po_box,
                'zipcode_desc' => $merchant->zipcode_desc,
                'zipcode' => $merchant->zipcode,
                'phones' => $merchant->phones->pluck('phone')->implode(' | '),
                'mobiles' => $merchant->mobiles->pluck('mobile')->implode(' | '),
                'emails' => $merchant->emails->pluck('email')->implode(' | '),
                'faxes' => $merchant->faxes->pluck('fax')->implode(' | '),
                'registered_date' => optional($merchant->registered_date)->format('Y-m-d'),
                'commercial_reg_date' => optional($merchant->commercial_reg_date)->format('Y-m-d'),
                'contacted' => $merchant->contacted ? 'نعم' : 'لا',
                'invited' => $merchant->invited ? 'نعم' : 'لا',
                'notes' => $merchant->notes,
                default => '',
            };
        }

        return $row;
    }

    private function resolveSorting(Request $request): array
    {
        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = [
            'id',
            'membership_no',
            'organization_name',
            'commercial_name',
            'commercial_reg_no',
            'org_national_no',
            'sector',
            'street',
            'registered_date',
            'commercial_reg_date',
            'contacted',
            'invited',
            'created_at',
        ];

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'id';
        }

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        return [$sort, $direction];
    }

    private function resolvePerPage(Request $request): int
    {
        $perPage = (int) $request->get('per_page', 20);

        return in_array($perPage, [10, 25, 50, 100, 200], true) ? $perPage : 20;
    }

    private function getSelectedIds(Request $request): array
    {
        $selectedIds = $request->input('selected_ids', []);

        if (!is_array($selectedIds)) {
            return [];
        }

        return collect($selectedIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function merchantRelationsForList(): array
    {
        return ['phones', 'mobiles', 'emails', 'faxes'];
    }

    private function extractLoggableMerchantValues(Merchant $merchant): array
    {
        return [
            'organization_name' => $merchant->organization_name,
            'membership_no' => $merchant->membership_no,
            'commercial_name' => $merchant->commercial_name,
            'commercial_reg_no' => $merchant->commercial_reg_no,
            'org_national_no' => $merchant->org_national_no,
            'sector' => $merchant->sector,
            'street' => $merchant->street,
            'description' => $merchant->description,
            'delegate_to_sign_on_management' => $merchant->delegate_to_sign_on_management,
            'members' => $merchant->members,
            'po_box' => $merchant->po_box,
            'zipcode_desc' => $merchant->zipcode_desc,
            'zipcode' => $merchant->zipcode,
            'registered_date' => optional($merchant->registered_date)->format('Y-m-d'),
            'commercial_reg_date' => optional($merchant->commercial_reg_date)->format('Y-m-d'),
            'sub_date' => optional($merchant->sub_date)->format('Y-m-d'),
            'ccate_id' => $merchant->ccate_id,
            'contacted' => $merchant->contacted,
            'invited' => $merchant->invited,
            'notes' => $merchant->notes,
            'phones' => $merchant->relationLoaded('phones') ? $merchant->phones->pluck('phone')->values()->all() : [],
            'mobiles' => $merchant->relationLoaded('mobiles') ? $merchant->mobiles->pluck('mobile')->values()->all() : [],
            'emails' => $merchant->relationLoaded('emails') ? $merchant->emails->pluck('email')->values()->all() : [],
            'faxes' => $merchant->relationLoaded('faxes') ? $merchant->faxes->pluck('fax')->values()->all() : [],
        ];
    }



//    public function printSingle(Merchant $merchant)
//    {
//        $merchant->load($this->merchantRelationsForList());
//
//        $columns = [
//            'membership_no',
//            'organization_name',
//            'commercial_name',
//            'commercial_reg_no',
//            'org_national_no',
//            'sector',
//            'street',
//            'description',
//            'delegate',
//            'members',
//            'po_box',
//            'zipcode_desc',
//            'zipcode',
//            'phones',
//            'mobiles',
//            'emails',
//            'faxes',
//            'registered_date',
//            'commercial_reg_date',
//            'contacted',
//            'invited',
//            'notes',
//        ];
//
//        return view('merchants.print', [
//            'merchants' => collect([$merchant]),
//            'columns' => $columns,
//        ]);
//    }

//    public function exportSingle(Merchant $merchant): \Symfony\Component\HttpFoundation\StreamedResponse
//    {
//        $merchant->load($this->merchantRelationsForList());
//
//        $columns = [
//            'membership_no',
//            'organization_name',
//            'commercial_name',
//            'commercial_reg_no',
//            'org_national_no',
//            'sector',
//            'street',
//            'description',
//            'delegate',
//            'members',
//            'po_box',
//            'zipcode_desc',
//            'zipcode',
//            'phones',
//            'mobiles',
//            'emails',
//            'faxes',
//            'registered_date',
//            'commercial_reg_date',
//            'contacted',
//            'invited',
//            'notes',
//        ];
//
//        return response()->streamDownload(function () use ($merchant, $columns) {
//            $handle = fopen('php://output', 'w');
//
//            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
//            fputcsv($handle, $this->buildCsvHeader($columns));
//            fputcsv($handle, $this->buildCsvRow($merchant, $columns));
//
//            fclose($handle);
//        }, 'merchant-' . $merchant->id . '.csv', [
//            'Content-Type' => 'text/csv; charset=UTF-8',
//        ]);
//    }


    public function printSingle(Merchant $merchant)
    {
        $merchant->load($this->merchantRelationsForList());

        return view('merchants.print-single', compact('merchant'));
    }
}
