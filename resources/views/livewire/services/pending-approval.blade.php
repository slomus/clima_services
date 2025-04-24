<section class="w-full px-4 sm:px-6 lg:px-8">
    @include('partials.services-heading')

    <x-services.layout :heading="__('Lista zgłoszeń serwisowych')" :subheading="__('Wszystkie zgłoszenia oczekujące na zatwierdzenie')">
        <!-- Wyszukiwanie i filtrowanie -->
        <div class="flex flex-col sm:flex-row justify-between mb-4 gap-4">
            <!-- Wyszukiwanie -->
            <flux:input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Szukaj zgłoszenia..."
                class="w-full sm:w-auto rounded-md px-4 py-2 border border-gray-300 focus:outline-none focus:ring focus:border-blue-500 dark:border-gray-700 dark:text-gray-200"
            />

            <!-- Filtrowanie po kliencie -->
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
                            Opis
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-gray-300">
                            <flux:button wire:click="sortBy('created_at')" class="group inline-flex items-center">
                                Data zgłoszenia
                                @if($sortField === 'created_at')
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
                            Akcje
                        </th>
                    </tr>
                </thead>
                <tbody class="border divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($pendingServices as $service)
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
                                    {{ \Illuminate\Support\Str::limit($service->description, 100) }}
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ \Carbon\Carbon::parse($service->created_at)->format('d.m.Y H:i') }}
                                </div>
                            </td>
                            <td class="px-4 py-4 flex items-center gap-2">
                                <button wire:click="openApproveModal({{ $service->id }})">
                                    <flux:icon.check-circle class="text-green-600 hover:text-green-100 dark:hover:text-green-400"/>
                                </button>
                                <button wire:click="openRejectModal({{ $service->id }})">
                                    <flux:icon.x-circle class="text-red-600 hover:text-red-100 dark:hover:text-red-400"/>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 px-4 text-center text-gray-400">
                                Brak zgłoszeń oczekujących na zatwierdzenie.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $pendingServices->links() }}
        </div>

        <!-- Approve Modal -->
        @if($showApproveModal)
            <flux:modal name="approve-modal" :show="true" focusable class="max-w-lg">
                <div>
                    <flux:heading size="lg">{{ __('Zatwierdzenie zgłoszenia serwisowego') }}</flux:heading>

                    <flux:subheading>
                        {{ __('Przydziel technika i zaplanuj datę serwisu') }}
                    </flux:subheading>
                </div>

                <div class="mt-4 space-y-4">
                    <!-- Przypisanie technika -->
                    <div>
                        <flux:select
                            wire:model.live="selectedTechnician"
                            :label="__('Technik')"
                        >
                            <flux:select.option value="">{{ __('-- Wybierz technika --') }}</flux:select.option>
                            @foreach($technicians as $technician)
                                <flux:select.option value="{{ $technician->id }}">
                                    {{ $technician->first_name }} {{ $technician->last_name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('selectedTechnician') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Data serwisu -->
                    <div>
                        <flux:input
                            wire:model="serviceDate"
                            type="datetime-local"
                            :label="__('Data serwisu')"
                        />
                        @error('serviceDate') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <flux:modal.close>
                        <flux:button variant="filled">{{ __('Anuluj') }}</flux:button>
                    </flux:modal.close>

                    <flux:button variant="primary" wire:click="approveService">{{ __('Zatwierdź zgłoszenie') }}</flux:button>
                </div>
            </flux:modal>
        @endif

        <!-- Reject Modal -->
        @if($showRejectModal)
            <flux:modal name="reject-modal" :show="true" focusable class="max-w-lg">
                <div>
                    <flux:heading size="lg">{{ __('Odrzucenie zgłoszenia serwisowego') }}</flux:heading>

                    <flux:subheading>
                        {{ __('Podaj powód odrzucenia zgłoszenia') }}
                    </flux:subheading>
                </div>

                <div class="mt-4">
                    <label for="rejectReason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Powód odrzucenia') }}
                    </label>
                    <div class="mt-1">
                        <flux:textarea
                            wire:model="rejectReason"
                            rows="4"
                            placeholder="Opisz powód odrzucenia zgłoszenia..."
                        />
                    </div>
                    @error('rejectReason') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <flux:modal.close>
                        <flux:button variant="filled">{{ __('Anuluj') }}</flux:button>
                    </flux:modal.close>

                    <flux:button variant="danger" wire:click="rejectService">{{ __('Odrzuć zgłoszenie') }}</flux:button>
                </div>
            </flux:modal>
        @endif
    </x-services.layout>
</section>
