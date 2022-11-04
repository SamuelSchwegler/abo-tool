<?php

namespace App\Exports;

use App\Models\Delivery;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DayAddresses implements FromCollection, WithMapping, ShouldAutoSize, WithHeadings, WithStyles, WithEvents
{
    public Carbon $date;
    public Collection $deliveries;
    public array $product_summary = [];
    public array $bold_rows = [];

    public function __construct(Carbon $date)
    {
        $this->date = $date;

        $ordersQuery = Order::isActive()->whereHas('delivery', function ($query) {
            $query->where('date', '>=', $this->date->format('Y-m-d'))
                ->where('date', '<=', $this->date->copy()->addDay()->format('Y-m-d'));
        });

        $products = $ordersQuery->get()->groupBy('product.name');
        foreach ($products as $name => $items) {
            $this->product_summary[] = [$name, $items->count()];
        }

        $this->deliveries = Delivery::where('date', '>=', $this->date->format('Y-m-d'))
            ->where('date', '<=', $this->date->copy()->addDay()->format('Y-m-d'))->get();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->deliveries;
    }

    public function map($delivery): array
    {
        if (sizeof($this->bold_rows) === 0) {
            $product_summary_offset = sizeof($this->product_summary);
            if ($product_summary_offset > 0)
                $product_summary_offset++;

            $this->bold_rows[] = (4 + $product_summary_offset);
            $this->bold_rows[] = (5 + $product_summary_offset);
        }

        $before = max($this->bold_rows);
        $orders_count = $delivery->active_orders->count();
        $this->bold_rows[] = $before + $orders_count + 2;
        $this->bold_rows[] = $before + $orders_count + 3;

        $data = [
            [null],
            [$delivery->delivery_service->name],
            ['Vorname', 'Nachname', 'Strasse', 'PLZ', 'Ort', 'Produkt']
        ];

        foreach ($delivery->active_orders as $order) {
            $customer = $order->customer;
            $address = $customer->delivery_address;

            $data[] = [
                'first name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'street' => $address?->street,
                'postcode' => $address?->postcode,
                'city' => $address?->city,
                'product' => $order->product->name,
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        $headings = [
            ['Adressen für Lieferungen am ' . $this->date->format('d.m.Y')],
            ['Exportiert am ' . now()->format('d.m.Y') . ' von ' . Auth::user()->email],
            []
        ];

        foreach ($this->product_summary as $array)
            $headings[] = $array;

        if (sizeof($this->product_summary) > 0)
            $headings[] = [];

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]]
        ];

        foreach ($this->bold_rows as $row)
            $styles[$row] = ['font' => ['bold' => true]];

        return $styles;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                //set logo to header
                $sheet->mergeCells('A1:G1'); // Titel
                $sheet->mergeCells('A2:G2'); // Name
            },
        ];
    }
}
