<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PendingCheckoutExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $checkouts;

    public function __construct($checkouts)
    {
        $this->checkouts = $checkouts;
    }

    public function collection()
    {
        return $this->checkouts->map(function ($checkout) {
            return [
                'ID' => $checkout->id,
                'Name' => $checkout->contact_person_name,
                'Phone' => $checkout->phone,
                'Email' => $checkout->email ?? 'N/A',
                'Shipping Address' => $checkout->shipping_address,
                'City' => $checkout->city ?? 'N/A',
                'Thana' => $checkout->thana ?? 'N/A',
                'Zip' => $checkout->zip ?? 'N/A',
                'Country' => $checkout->country ?? 'N/A',
                'Total Amount' => $checkout->total_amount,
                'Status' => $checkout->status,
                'Date' => $checkout->created_at->format('d M Y h:i A'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Phone',
            'Email',
            'Shipping Address',
            'City',
            'Thana',
            'Zip',
            'Country',
            'Total Amount',
            'Status',
            'Date',
        ];
    }
}
