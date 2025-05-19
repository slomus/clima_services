<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\Service;
use App\Models\Device;
use App\Models\User;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Exports\ServicesExport;

class Index extends Component
{
    public $from;
    public $to;

    public $serviceCount = 0;
    public $salesSum = 0;
    public $deviceFailures = [];
    public $costsSum = 0;
    public $incomeSum = 0;
    public $technicianStats = [];

    public function mount()
    {
        $this->from = now()->startOfMonth()->format('Y-m-d');
        $this->to = now()->endOfMonth()->format('Y-m-d');
        $this->generateReport();
    }

    public function updated($field)
    {
        if (in_array($field, ['from', 'to'])) {
            $this->generateReport();
        }
    }

    public function generateReport()
    {
        $this->serviceCount = Service::whereBetween('service_date', [$this->from, $this->to])->count();

        $this->incomeSum = Invoice::whereBetween('created_at', [$this->from, $this->to])->sum('amount');

        $this->costsSum = 0;

        $this->deviceFailures = Service::select('device_id', DB::raw('count(*) as failures'))
            ->whereBetween('service_date', [$this->from, $this->to])
            ->groupBy('device_id')
            ->with('device')
            ->orderByDesc('failures')
            ->take(10)
            ->get();

        $this->technicianStats = Service::select('user_id', DB::raw('count(*) as count'))
            ->whereBetween('service_date', [$this->from, $this->to])
            ->groupBy('user_id')
            ->with('user')
            ->orderByDesc('count')
            ->get();
    }

    public function exportCsv()
    {
        return Excel::download(new ServicesExport($this->from, $this->to), 'raport_serwisy.csv');
    }

    public function exportXlsx()
    {
        return Excel::download(new ServicesExport($this->from, $this->to), 'raport_serwisy.xlsx');
    }

    public function exportPdf()
    {
        $data = [
            'from' => $this->from,
            'to' => $this->to,
            'serviceCount' => $this->serviceCount,
            'incomeSum' => $this->incomeSum,
            'costsSum' => $this->costsSum,
            'deviceFailures' => $this->deviceFailures,
            'technicianStats' => $this->technicianStats,
        ];

        $pdf = Pdf::loadView('exports.services-pdf', $data);

        return response()->streamDownload(
            fn () => print($pdf->stream()),
            'raport_serwisy_' . now()->format('Ymd_His') . '.pdf'
        );
    }

    public function render()
    {
        return view('livewire.reports.index');
    }
}
