<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\DeliveryCharge;
use Illuminate\Http\Request;

class DeliveryChargeController extends Controller
{
    /**
     * Show delivery charge edit form
     */
    public function edit()
    {
        // Single row system
        $deliveryCharge = DeliveryCharge::first();

        // If no data exists, create default
        if (!$deliveryCharge) {
            $deliveryCharge = DeliveryCharge::create([
                'local_delivery_charge' => 0,
                'country_delivery_charge' => 0,
            ]);
        }

        return view(
            'admin-views.product.delivery-charge.edit',
            compact('deliveryCharge')
        );
    }

    /**
     * Update delivery charge
     */
    public function update(Request $request)
    {
        $request->validate([
            'local_delivery_charge' => 'required|numeric|min:0',
            'country_delivery_charge' => 'required|numeric|min:0',
        ]);

        $deliveryCharge = DeliveryCharge::first();

        $deliveryCharge->update([
            'local_delivery_charge' => $request->local_delivery_charge,
            'country_delivery_charge' => $request->country_delivery_charge,
        ]);

        return redirect()->back()->with('success', 'Delivery charge updated successfully');
    }
}
