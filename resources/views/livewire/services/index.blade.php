<section class="w-full px-4 sm:px-6 lg:px-8">
    @include('partials.services-heading')

    <x-services.layout :heading="__('Lista zgłoszeń serwisowych')" :subheading="__('Wszystkie zgłoszenia serwisowe w systemie')">
        <!-- Wyszukiwanie i filtrowanie -->
        <div class="flex flex-col sm:flex-row justify-between mb-4 gap-4">
            <!-- Wyszukiwanie -->
            <flux:input
                type="text"
                wire:model.live.debounce.100ms="search"
                placeholder="Szukaj zgłoszenia..."
                class="w-full sm:w-auto rounded-md px-4 py-2 border border-gray-300 focus:outline-none focus:ring focus:border-blue-500 dark:border-gray-700 dark:text-gray-200"
            />

            <!-- Filtrowanie po statusie -->
            <flux:select
                wire:model.live="status"
                placeholder="Wybierz status"
                class="w-full sm:w-auto rounded-md px-4 py-2 border border-gray-300 focus:outline-none focus:ring focus:border-blue-500 dark:border-gray-700 dark:text-gray-200"
            >
                <flux:select.option value="">Wszystkie statusy</flux:select.option>
                <flux:select.option value="reported">Zgłoszone</flux:select.option>
                <flux:select.option value="planned">Zaplanowane</flux:select.option>
                <flux:select.option value="in_progress">W trakcie</flux:select.option>
                <flux:select.option value="completed">Zakończone</flux:select.option>
                <flux:select.option value="failed">Nieudane</flux:select.option>
            </flux:select>

            <!-- Filtrowanie po kliencie -->
            @if(auth()->check() && (auth()->user()->can('tickets.view_all') || auth()->user()->can('tickets.view_assigned')))
                <flux:select
                    wire:model.live="client_id"
                    placeholder="Wybierz klienta"
                    class="w-full sm:w-auto rounded-md px-4 py-2 border border-gray-300 focus:outline-none focus:ring focus:border-blue-500 dark:border-gray-700 dark:text-gray-200"
                >
                    <flux:select.option value="">Wszyscy klienci</flux:select.option>
                    @foreach($clients as $client)
                        <flux:select.option value="{{ $client->id }}">{{ $client->first_name }} {{ $client->last_name }}</flux:select.option>
                    @endforeach
                </flux:select>
            @endif

        </div>

        <!-- Messages -->
        @if (session()->has('message'))
            <div class="mb-4 bg-green-800 border border-green-600 text-green-200 px-4 py-3 rounded relative">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 bg-red-800 border border-red-600 text-red-200 px-4 py-3 rounded relative">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tabela zgłoszeń -->
        <div class="overflow-x-auto border shadow-md rounded-lg">
            <table class="w-full table-auto divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="sticky top-0 z-[1]">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-gray-300">
                            <flux:button wire:click="sortBy('client_id')" class="group inline-flex items-center">
                                Klient
                                @if($sortField === 'client_id')
                                    <span class="ml-1">
                                        @if($sortDirection === 'asc')
                                            ↑
                                        @else
                                            ↓
                                        @endif
                                    </span>
                                @endif
                            </flux:button>
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-gray-300">
                            <flux:button wire:click="sortBy('device_id')" class="group inline-flex items-center">
                                Urządzenie
                                @if($sortField === 'device_id')
                                    <span class="ml-1">
                                        @if($sortDirection === 'asc')
                                            ↑
                                        @else
                                            ↓
                                        @endif
                                    </span>
                                @endif
                            </flux:button>
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-gray-300">
                            <flux:button wire:click="sortBy('user_id')" class="group inline-flex items-center">
                                Technik
                                @if($sortField === 'user_id')
                                    <span class="ml-1">
                                        @if($sortDirection === 'asc')
                                            ↑
                                        @else
                                            ↓
                                        @endif
                                    </span>
                                @endif
                            </flux:button>
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-gray-300">
                            <flux:button wire:click="sortBy('service_date')" class="group inline-flex items-center">
                                Data serwisu
                                @if($sortField === 'service_date')
                                    <span class="ml-1">
                                        @if($sortDirection === 'asc')
                                            ↑
                                        @else
                                            ↓
                                        @endif
                                    </span>
                                @endif
                            </flux:button>
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-gray-300">
                            Status
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-gray-300">
                            Akcje
                        </th>
                    </tr>
                </thead>
                <tbody class="border divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($services as $service)
                        <tr class="border hover:bg-gray-100 dark:hover:bg-gray-600">
                            <td class="px-4 py-4">
                                @if($service->client)
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $service->client->first_name }} {{ $service->client->last_name }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $service->client->email }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Klient usunięty</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @if($service->device)
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $service->device->model }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $service->device->serial_number }}</div>
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Urządzenie usunięte</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    @if($service->user)
                                        {{ $service->user->first_name }} {{ $service->user->last_name }}
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">Technik nieprzypisany</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ \Carbon\Carbon::parse($service->service_date)->format('d.m.Y H:i') }}
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($service->status === 'reported') bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200
                                    @elseif($service->status === 'planned') bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200
                                    @elseif($service->status === 'in_progress') bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-200
                                    @elseif($service->status === 'completed') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200
                                    @elseif($service->status === 'failed') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200
                                    @endif">
                                    @if($service->status === 'reported') Zgłoszone
                                    @elseif($service->status === 'planned') Zaplanowane
                                    @elseif($service->status === 'in_progress') W trakcie
                                    @elseif($service->status === 'completed') Zakończone
                                    @elseif($service->status === 'failed') Nieudane
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-4 flex items-center gap-2">
                                <!-- Edit -->
                                @if(auth()->user()->can('tickets.manage_assigned.edit'))
                                <a href="{{ route('services.edit', $service->id) }}">
                                    <flux:icon.pencil class="text-blue-600 hover:text-blue-100 dark:hover:text-blue-400"/>
                                </a>
                                @endif

                                <!-- Mark as completed -->
                                @if(auth()->user()->can('tickets.manage_assigned.edit') && $service->status !== 'completed')
                                    <flux:modal.trigger name="invoice-modal">
                                        <button wire:click="openInvoiceModal({{ $service->id }})" class="text-green-400 hover:text-green-300"><flux:icon.check-circle class="text-green-600 hover:text-green-100 dark:hover:text-green-400"/></button>
                                    </flux:modal.trigger>
                                @endif

                                <!-- Delete -->
                                @if(auth()->user()->can('tickets.delete'))
                                <flux:modal.trigger name="confirm-service-deletion-{{ $service->id }}">
                                    <flux:icon.trash wire:click="confirmDelete({{ $service->id }})" class="text-red-600 hover:text-red-100 dark:hover:text-red-400"/>
                                </flux:modal.trigger>
                                @endif

                                @if($service->status === 'completed' && $service->invoice)
                                    <a
                                        href="{{ route('services.download-invoice', $service->invoice->id) }}"
                                        target="_blank"
                                        class="text-indigo-600 hover:text-indigo-400 flex items-center gap-1"
                                        title="Pobierz fakturę"
                                    >
                                        <flux:icon.arrow-down-tray class="w-5 h-5"/>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 px-4 text-center text-gray-400">
                                Brak zgłoszeń serwisowych spełniających kryteria wyszukiwania.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $services->links() }}
        </div>

        <!-- Modal potwierdzenia -->
        @foreach($services as $service)
            <flux:modal name="confirm-service-deletion-{{ $service->id }}" focusable class="max-w-lg">
                <div>
                    <flux:heading size="lg">{{ __('Jesteś pewien, że chcesz usunąć zgłoszenie?') }}</flux:heading>

                    <flux:subheading>
                        {{ __('Po usunięciu zgłoszenia wszystkie jego dane zostaną na stałe usunięte.') }}
                    </flux:subheading>
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <flux:modal.close>
                        <flux:button variant="filled">{{ __('Anuluj') }}</flux:button>
                    </flux:modal.close>

                    <flux:button variant="danger" wire:click="deleteService">{{ __('Usuń zgłoszenie') }}</flux:button>
                </div>
            </flux:modal>
        @endforeach


        <!-- Modal faktury w stylu Flux -->
        @if($invoiceModalOpen)
            <flux:modal name="invoice-modal" focusable class="max-w-lg">
                <div>
                    <flux:heading size="lg">Podaj kwotę faktury</flux:heading>
                </div>
                <form wire:submit.prevent="finishServiceAndGenerateInvoice">
                    <div class="my-6">
                        <flux:input
                            type="number"
                            step="0.01"
                            min="0.01"
                            wire:model="invoiceAmount"
                            :label="'Kwota brutto (PLN)'"
                            required
                        />
                        @error('invoiceAmount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end space-x-2">
                        <flux:modal.close>
                            <flux:button variant="filled">Anuluj</flux:button>
                        </flux:modal.close>
                        <flux:button variant="primary" type="submit">Zakończ serwis i wystaw fakturę</flux:button>
                    </div>
                </form>
            </flux:modal>
        @endif
    </x-services.layout>
</section>
