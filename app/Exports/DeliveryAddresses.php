<?php

namespace App\Exports;

use App\Models\Delivery;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DeliveryAddresses implements FromCollection, WithMapping, ShouldAutoSize, WithHeadings, WithStyles
{
    protected Delivery $delivery;

    public function __construct(Delivery $delivery)
    {
        $this->delivery = $delivery;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->delivery->active_orders;
    }

    public function map($order): array
    {
        $customer = $order->customer;
        $address = $customer->delivery_address;

        return [
            'first name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'street' => $address?->street,
            'postcode' => $address?->postcode,
            'city' => $address?->city,
            'product' => $order->product->name,
        ];
    }

    public function headings(): array
    {
        return [
            ['Adressen für '.$this->delivery->delivery_service->name.' am '.$this->delivery->date->format('d.m.Y')],
            ['Exportiert am '.now()->format('d.m.Y').' von '.Auth::user()->email],
            ['Vorname', 'Nachname', 'Strasse', 'PLZ', 'Ort', 'Produkt'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
            2    => ['font' => ['bold' => true]],
            3    => ['font' => ['bold' => true]],
        ];
    }
}
