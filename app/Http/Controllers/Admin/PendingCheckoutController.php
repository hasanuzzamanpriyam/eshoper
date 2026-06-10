<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\PendingCheckout;
use App\Exports\PendingCheckoutExport;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PendingCheckoutController extends Controller
{
    public function list(Request $request)
    {
        $query = PendingCheckout::query();

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('contact_person_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('from_date') && $request->from_date != '') {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date != '') {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $checkouts = $query->latest()->paginate(Helpers::pagination_limit());

        return view('admin-views.pending-checkout.list', compact('checkouts'));
    }

    public function details($id)
    {
        $checkout = PendingCheckout::with(['customer', 'order'])->findOrFail($id);
        return view('admin-views.pending-checkout.details', compact('checkout'));
    }

    public function destroy($id)
    {
        $checkout = PendingCheckout::findOrFail($id);
        $checkout->delete();
        Toastr::success(translate('pending_checkout_deleted_successfully'));
        return back();
    }

    public function export(Request $request)
    {
        $query = PendingCheckout::query();

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $checkouts = $query->latest()->get();

        return Excel::download(new PendingCheckoutExport($checkouts), 'pending-checkouts.xlsx');
    }
}
