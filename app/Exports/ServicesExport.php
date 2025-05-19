<?php

namespace App\Exports;

use App\Models\Service;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ServicesExport implements FromCollection, WithHeadings
{
    protected $from;
    protected $to;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Service::with(['device', 'user'])
            ->whereBetween('service_date', [$this->from, $this->to])
            ->get()
            ->map(function ($service) {
                return [
                    'Data' => $service->service_date,
                    'Urządzenie' => $service->device?->model . ' (' . $service->device?->serial_number . ')',
                    'Technik' => $service->user?->first_name . ' ' . $service->user?->last_name,
                    'Status' => $service->status,
                ];
            });
    }

    public function headings(): array
    {
        return ['Data', 'Urządzenie', 'Technik', 'Status'];
    }
}
