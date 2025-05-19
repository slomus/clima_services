<section class="w-full">
    @include('partials.services-heading')
    <x-services.layout :heading="__('Edycja zgłoszenia #' . $service->id)" :subheading="__('Edytuj szczegóły zgłoszenia')">
        <div class="my-6 w-full">
            @if (session()->has('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <form wire:submit="save" class="space-y-6">
                <!-- Klient -->
                @if(auth()->user()->can('tickets.manage_assigned.edit'))
                    <div>
                        <flux:select wire:model.live="client_id" :label="__('Klient')" :filter="false">
                            <flux:select.option value="">{{ __('Wybierz klienta') }}</flux:select.option>
                            @foreach($clients as $client)
                                <flux:select.option value="{{ $client->id }}">{{ $client->first_name }} {{ $client->last_name }} ({{ $client->email }})</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('client_id') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                    </div>
                @else
                    <div>
                        <label for="client" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Klient') }}</label>
                        <div class="mt-1 p-2 bg-gray-50 rounded-md border border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                            {{ $service->client->first_name ?? '' }} {{ $service->client->last_name ?? '' }}
                        </div>
                    </div>
                @endif

                <!-- Urządzenie -->
                <div>
                    @if(auth()->user()->can('tickets.manage_assigned.edit'))
                        <flux:select wire:model="device_id" :label="__('Urządzenie')" :filter="false">
                            <flux:select.option value="">{{ __('Wybierz urządzenie') }}</flux:select.option>
                            @foreach($devices as $device)
                                <flux:select.option value="{{ $device->id }}">{{ $device->model }} ({{ $device->serial_number }})</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('device_id') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                    @else
                        <label for="device" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Urządzenie') }}</label>
                        <div class="mt-1 p-2 bg-gray-50 rounded-md border border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                            {{ $service->device->model ?? '' }} ({{ $service->device->serial_number ?? '' }})
                        </div>
                    @endif
                </div>

                <!-- Technik -->
                <div>
                    @if(auth()->user()->can('tickets.manage_assigned.edit'))
                        <flux:select wire:model="user_id" :label="__('Technik')" :filter="false">
                            <flux:select.option value="">{{ __('Wybierz technika') }}</flux:select.option>
                            @foreach($technicians as $technician)
                                <flux:select.option value="{{ $technician->id }}">{{ $technician->first_name }} {{ $technician->last_name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('user_id') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                    @else
                        <label for="technician" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Technik') }}</label>
                        <div class="mt-1 p-2 bg-gray-50 rounded-md border border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                            {{ $service->user->first_name ?? '' }} {{ $service->user->last_name ?? '' }}
                        </div>
                    @endif
                </div>

                <!-- Data serwisu -->
                <div>
                    @if(auth()->user()->can('tickets.manage_assigned.edit'))
                        <flux:input wire:model="service_date" :label="__('Data serwisu')" type="datetime-local" />
                        @error('service_date') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                    @else
                        <label for="service_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Data serwisu') }}</label>
                        <div class="mt-1 p-2 bg-gray-50 rounded-md border border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                            {{ \Carbon\Carbon::parse($service->service_date)->format('d.m.Y H:i') }}
                        </div>
                    @endif
                </div>

                <!-- Status -->
                <div>
                    @if(auth()->user()->can('tickets.manage_assigned.edit'))
                        <flux:select wire:model="status" :label="__('Status')" :filter="false">
                            <flux:select.option value="reported">{{ __('Zgłoszone') }}</flux:select.option>
                            <flux:select.option value="planned">{{ __('Zaplanowane') }}</flux:select.option>
                            <flux:select.option value="in_progress">{{ __('W trakcie') }}</flux:select.option>
                            <flux:select.option value="completed">{{ __('Zakończone') }}</flux:select.option>
                            <flux:select.option value="failed">{{ __('Nieudane') }}</flux:select.option>
                        </flux:select>
                        @error('status') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                    @else
                        <label for="status" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Status') }}</label>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
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
                        </div>
                    @endif
                </div>

                <!-- Opis problemu -->
                <div>
                    <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Opis problemu') }}</label>
                    <div class="mt-1">
                        <textarea
                            wire:model="description"
                            id="description"
                            rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            placeholder="Opisz szczegółowo problem z urządzeniem..."
                            {{ auth()->user()->can('tickets.manage_assigned.edit') || auth()->user()->can('tickets.view_own') ? '' : 'disabled' }}
                        ></textarea>
                    </div>
                    @error('description') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Przyciski formularza -->
                <div class="flex items-center justify-end gap-4 pt-4">
                    <flux:link :href="route('services.index')">
                        {{ __('Anuluj') }}
                    </flux:link>

                    @if(auth()->user()->can('tickets.manage_assigned.edit') ||
                       (auth()->user()->can('tickets.view_own') && $service->client_id === auth()->id()))
                        <flux:button type="submit">
                            {{ __('Zapisz zmiany') }}
                        </flux:button>
                    @endif
                </div>
            </form>
        </div>
    </x-services.layout>
</section>
