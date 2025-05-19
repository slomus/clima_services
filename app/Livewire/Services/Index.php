<?php

namespace App\Livewire\Services;

use App\Models\Service;
use App\Models\User;
use App\Models\Device;
use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $client_id = null;

    public $deleteModalOpen = false;
    public $serviceToDelete = null;

    public $invoiceModalOpen = false;
    public $invoiceAmount = null;
    public $invoiceServiceId = null;
    public $currentService = null;

    public $clients = [];

    public function mount()
    {
        if (auth()->check() && auth()->user()->hasRole('Client')) {
            $this->client_id = auth()->id();
        } elseif (auth()->check() && (auth()->user()->can('tickets.view_all') || auth()->user()->can('tickets.view_assigned'))) {
            $this->loadClients();
        }
    }

    public function loadClients()
    {
        try {
            $query = User::role('Client');
            if (!empty($this->search)) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            }
            $this->clients = $query->get();
        } catch (\Exception $e) {
            \Log::error('Error loading clients: ' . $e->getMessage());
            $this->clients = [];
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function confirmDelete($serviceId)
    {
        $this->serviceToDelete = $serviceId;
        $this->deleteModalOpen = true;
    }

    public function closeDeleteModal()
    {
        $this->deleteModalOpen = false;
        $this->serviceToDelete = null;
    }

    public function deleteService()
    {
        try {
            if (!auth()->user()->can('tickets.delete')) {
                session()->flash('error', 'Nie masz uprawnień do usuwania zgłoszeń.');
                $this->closeDeleteModal();
                return;
            }

            $service = Service::find($this->serviceToDelete);
            if ($service) {
                if ($service->calendars) {
                    $service->calendars()->delete();
                }
                $service->delete();
                session()->flash('message', 'Zgłoszenie zostało usunięte.');
            }

            $this->closeDeleteModal();
        } catch (\Exception $e) {
            \Log::error('Błąd podczas usuwania zgłoszenia: ' . $e->getMessage());
            session()->flash('error', 'Wystąpił błąd podczas usuwania zgłoszenia: ' . $e->getMessage());
            $this->closeDeleteModal();
        }
    }

    public function updateServiceStatus($serviceId, $newStatus)
    {
        if (!auth()->user()->can('tickets.manage_assigned.edit')) {
            session()->flash('error', 'Nie masz uprawnień do zmiany statusu zgłoszenia.');
            return;
        }

        $service = Service::findOrFail($serviceId);
        $service->update(['status' => $newStatus]);

        if ($service->calendars && $service->calendars->count() > 0) {
            $statusMap = [
                'reported' => 'planed',
                'planned' => 'planed',
                'in_progress' => 'in_progress',
                'completed' => 'completed',
                'failed' => 'cancelled'
            ];

            if (isset($statusMap[$newStatus])) {
                foreach ($service->calendars as $calendar) {
                    $calendar->update(['status' => $statusMap[$newStatus]]);
                }
            }
        }

        session()->flash('message', 'Status zgłoszenia został zaktualizowany.');
    }

    public function viewInvoice($serviceId)
    {
        $service = Service::with(['invoice'])->findOrFail($serviceId);

        if (!$service->invoice) {
            session()->flash('error', 'Faktura dla tego zgłoszenia nie istnieje.');
            return;
        }

        $this->currentService = $service;
        $this->invoiceModalOpen = true;
    }

    public function openInvoiceModal($serviceId)
    {
        $this->invoiceServiceId = $serviceId;
        $this->invoiceAmount = null;
        $this->invoiceModalOpen = true;
    }

    public function closeInvoiceModal()
    {
        $this->invoiceModalOpen = false;
        $this->currentService = null;
    }

    public function finishServiceAndGenerateInvoice()
    {
        $this->validate([
            'invoiceAmount' => 'required|numeric|min:0.01',
        ]);

        $service = Service::with(['device', 'client', 'user'])->findOrFail($this->invoiceServiceId);

        // Ustaw status na zakończony
        $service->status = 'completed';
        $service->save();

        // Utwórz fakturę
        $invoice = new Invoice();
        $invoice->client_id = $service->client_id;
        $invoice->for = 'Serwis urządzenia ' . $service->device->model . ' (' . $service->device->serial_number . ')';
        $invoice->issue_date = now();
        $invoice->due_date = now()->addDays(14);
        $invoice->amount = $this->invoiceAmount;
        $invoice->payment_status = 'new';
        $invoice->save();

        $service->invoice_id = $invoice->id;
        $service->save();

        // Generuj PDF i zapisz do storage
        $fileName = 'invoice-' . $invoice->id . '.pdf';
        $pdfContent = $this->generateInvoicePdf($invoice, $service);
        Storage::put('public/invoices/' . $fileName, $pdfContent);

        // Zaktualizuj ścieżkę pliku w fakturze
        $invoice->file_path = 'invoices/' . $fileName;
        $invoice->save();

        $this->invoiceModalOpen = false;
        $this->invoiceAmount = null;
        $this->invoiceServiceId = null;

        session()->flash('message', 'Serwis zakończony i faktura wystawiona.');
    }

    public function downloadInvoice($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $service = Service::where('invoice_id', $invoice->id)->first();

        $fileName = 'invoice-' . $invoice->id . '.pdf';
        $publicPath = storage_path('app/public/invoices/' . $fileName);
        $privatePath = storage_path('app/private/public/invoices/' . $fileName);

        if (file_exists($publicPath)) {
            return response()->download($publicPath);

        } elseif (file_exists($privatePath)) {
            return response()->download($privatePath);
        } else {
            // Możesz tu wygenerować PDF jeśli nie istnieje
            abort(404, 'Faktura nie istnieje.');
        }
    }

    protected function generateInvoicePdf($invoice, $service)
    {
        try {
            $pdf = Pdf::loadView('pdf.invoice', [
                'invoice' => $invoice,
                'service' => $service,
                'client' => $service->client,
                'company' => [
                    'name' => 'clima_service',
                    'address' => 'czekoladowa 12',
                    'phone' => '420133769',
                    'email' => 'clima@company.com',
                    'tax_id' => '1'
                ]
            ])->setOptions(['defaultFont' => 'DejaVu Sans']);
            return $pdf->output();
        } catch (\Exception $e) {
            \Log::error('PDF Generation failed: ' . $e->getMessage());
            return Pdf::loadHTML('<h1>Faktura</h1><p>Error generating PDF</p>')->output();
        }
    }

    public function render()
    {
        $query = Service::query()
            ->with(['device', 'client', 'user'])
            ->where('status', '!=', 'pending_approval');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('device', function ($q) {
                        $q->where('model', 'like', '%' . $this->search . '%')
                            ->orWhere('serial_number', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('client', function ($q) {
                        $q->where('first_name', 'like', '%' . $this->search . '%')
                            ->orWhere('last_name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        if (!empty($this->client_id)) {
            $query->where('client_id', $this->client_id);
        }

        if (auth()->check() && auth()->user()->hasRole('Client')) {
            $query->where('client_id', auth()->id());
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        $services = $query->paginate(10);

        return view('livewire.services.index', [
            'services' => $services
        ]);
    }
}
