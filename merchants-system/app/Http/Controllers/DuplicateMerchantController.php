<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DuplicateMerchantController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'membership_no');

        $allowedTypes = [
            'membership_no',
            'commercial_reg_no',
            'org_national_no',
        ];

        if (!in_array($type, $allowedTypes, true)) {
            $type = 'membership_no';
        }

        $duplicates = Merchant::query()
            ->select($type, DB::raw('COUNT(*) as duplicates_count'))
            ->whereNotNull($type)
            ->where($type, '!=', '')
            ->groupBy($type)
            ->having('duplicates_count', '>', 1)
            ->orderByDesc('duplicates_count')
            ->paginate(20)
            ->withQueryString();

        return view('duplicates.index', compact('duplicates', 'type'));
    }

    public function show(string $type, string $value)
    {
        $allowedTypes = [
            'membership_no',
            'commercial_reg_no',
            'org_national_no',
        ];

        if (!in_array($type, $allowedTypes, true)) {
            abort(404);
        }

        $merchants = Merchant::with(['phones', 'mobiles', 'emails', 'faxes'])
            ->where($type, $value)
            ->orderBy('id')
            ->get();

        if ($merchants->isEmpty()) {
            abort(404);
        }

        return view('duplicates.show', compact('merchants', 'type', 'value'));
    }

    public function destroy(Merchant $merchant)
    {
        $merchant->delete();

        return back()->with('success', 'تم حذف السجل المكرر');
    }
}
