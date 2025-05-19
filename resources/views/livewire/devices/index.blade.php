<section class="w-full px-4 sm:px-6 lg:px-8">
    @include('partials.devices-heading')

    <x-devices.layout :heading="__('Lista urządzeń')" :subheading="__('Wszystkie urządzenia w systemie')">
        <!-- Wyszukiwanie i filtrowanie -->
        <div class="flex flex-col sm:flex-row justify-between mb-4 gap-4">
            <!-- Wyszukiwanie -->
            <flux:input
                type="text"
                wire:model.live.debounce.100ms="search"
                placeholder="Szukaj urządzenia..."
                class="w-full sm:w-auto rounded-md px-4 py-2 border border-gray-300 focus:outline-none focus:ring focus:border-blue-500 dark:border-gray-700 dark:text-gray-200"
            />

            <!-- Filtrowanie po kliencie -->
            @if(auth()->check() && auth()->user()->can('devices.view_all'))
                <flux:select
                    wire:model.live="userFilter"
                    placeholder="Wybierz klienta"
                    class="w-full sm:w-auto rounded-md px-4 py-2 border border-gray-300 focus:outline-none focus:ring focus:border-blue-500 dark:border-gray-700 dark:text-gray-200"
                >
                    <flux:select.option value="">Wszyscy klienci</flux:select.option>
                    @foreach($clients as $client)
                        <flux:select.option value="{{ $client['id'] }}">{{ $client['first_name'] }} {{ $client['last_name'] }}</flux:select.option>
                    @endforeach
                </flux:select>
            @endif
        </div>

        <!-- Tabela urządzeń -->
        <div class="overflow-x-auto border shadow-md rounded-lg">
            <table class="w-full table-auto divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="sticky top-0 z-[1]">
                    <tr>
                        @if(auth()->check() && auth()->user()->can('devices.view_all'))
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-gray-300">
                                <flux:button wire:click="sortBy('client.id')" class="group inline-flex items-center">
                                    Klient
                                    @if($sortField === 'client.id')
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
                        @endif
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-gray-300">
                            <flux:button wire:click="sortBy('model')" class="group inline-flex items-center">
                                Model
                                @if($sortField === 'model')
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
                            <flux:button wire:click="sortBy('serial_number')" class="group inline-flex items-center">
                                Numer seryjny
                                @if($sortField === 'serial_number')
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
                            <flux:button wire:click="sortBy('purchase_date')" class="group inline-flex items-center">
                                Data zakupu
                                @if($sortField === 'purchase_date')
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
                            <flux:button wire:click="sortBy('warranty_end_date')" class="group inline-flex items-center">
                                Koniec gwarancji
                                @if($sortField === 'warranty_end_date')
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
                    @forelse($devices as $device)
                        <tr class="border hover:bg-gray-100 dark:hover:bg-gray-600">
                            @if(auth()->check() && auth()->user()->can('devices.view_all'))
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $device->user->first_name }} {{ $device->user->last_name }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $device->user->email }}
                                    </div>
                                </td>
                            @endif
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $device->model }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $device->producent_number }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $device->serial_number }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $device->purchase_date ? date('d.m.Y', strtotime($device->purchase_date)) : '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $device->warranty_end_date ? date('d.m.Y', strtotime($device->warranty_end_date)) : '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-4 flex items-center gap-2">
                                <flux:link wire:navigate href="{{ route('devices.edit', $device->id) }}">
                                    <flux:icon.pencil class="text-blue-600 hover:text-blue-100 dark:hover:text-blue-400"/>
                                </flux:link>

                                @if(auth()->check() && auth()->user()->can('devices.delete'))
                                    <flux:button wire:click="confirmDelete({{ $device->id }})" class="text-red-600 hover:text-red-100 dark:hover:text-red-400">
                                        <flux:icon.trash />
                                    </flux:button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->check() && auth()->user()->can('devices.view_all') ? '6' : '5' }}" class="py-6 px-4 text-center text-gray-400">
                                Brak urządzeń spełniających kryteria wyszukiwania.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginacja -->
        <div class="mt-4">
            {{ $devices->links() }}
        </div>

        <!-- Modal potwierdzenia usunięcia -->
        <flux:modal name="confim-device-deletion" focusable class="max-w-lg">
            <div>
                <flux:heading size="lg">{{ __('Jesteś pewien, że chcesz usunąć urządzenie?') }}</flux:heading>

                <flux:subheading>
                    {{ __('Po usunięciu urządzenia wszystkie jego dane zostaną na stałe usunięte.') }}
                </flux:subheading>

                <div class="mt-4">
                    <flux:input
                        type="password"
                        wire:model="password"
                        placeholder="Potwierdź hasło"
                        class="w-full rounded-md px-4 py-2 border border-gray-300 focus:outline-none focus:ring focus:border-blue-500 dark:border-gray-700 dark:text-gray-200"
                    />
                    @error('password') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-2">
                <flux:button variant="filled" wire:click="cancelDelete">{{ __('Anuluj') }}</flux:button>
                <flux:button variant="danger" wire:click="deleteDevice">{{ __('Usuń urządzenie') }}</flux:button>
            </div>
        </flux:modal>
    </x-devices.layout>
</section>