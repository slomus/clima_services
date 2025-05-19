<section class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <flux:heading size="xl" level="1">Raporty i analiza</flux:heading>
    </div>

    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
        <div class="flex gap-4">
            <div>
                <flux:input type="date" wire:model="from" :label="'Od'" class="w-36" />
            </div>
            <div>
                <flux:input type="date" wire:model="to" :label="'Do'" class="w-36" />
            </div>
        </div>
        <div class="flex gap-2">
            <flux:button wire:click="exportCsv" variant="primary">Eksport CSV</flux:button>
            <flux:button wire:click="exportXlsx" variant="primary">Eksport XLSX</flux:button>
            <flux:button wire:click="exportPdf" variant="primary">Eksport PDF</flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow">
            <h2 class="font-bold mb-2">Statystyka serwisów</h2>
            <div>Liczba serwisów: <b>{{ $serviceCount }}</b></div>
            <div>Przychody: <b>{{ number_format($incomeSum, 2) }} zł</b></div>
            <div>Koszty: <b>{{ number_format($costsSum, 2) }} zł</b></div>
        </div>
        <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow">
            <h2 class="font-bold mb-2">Awaryjność urządzeń</h2>
            <ul>
                @foreach($deviceFailures as $row)
                    <li>{{ $row->device->model ?? 'Brak' }} ({{ $row->device->serial_number ?? '' }}): <b>{{ $row->failures }}</b></li>
                @endforeach
            </ul>
        </div>
        <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow col-span-1 md:col-span-2">
            <h2 class="font-bold mb-2">Efektywność serwisantów</h2>
            <ul>
                @foreach($technicianStats as $row)
                    <li>{{ $row->user->first_name ?? 'Brak' }} {{ $row->user->last_name ?? '' }}: <b>{{ $row->count }}</b> serwisów</li>
                @endforeach
            </ul>
        </div>
    </div>
</section>