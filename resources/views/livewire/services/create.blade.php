<section class="w-full">
    @include('partials.services-heading')
    <x-services.layout :heading="__('Dodaj zgłoszenie serwisowe')" :subheading="__('Utwórz nowe zgłoszenie serwisowe')">
        <div class="my-6 w-full">
            @if (session()->has('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <form wire:submit="save" class="space-y-6">
                <!-- Klient -->
                @if(auth()->check() && (auth()->user()->can('tickets.view_all') || auth()->user()->can('tickets.view_assigned')))
                    <div>
                        <flux:select wire:model.live="client_id" wire:change="loadDevices" :label="__('Klient')" :filter="false">
                            <flux:select.option value="">{{ __('Wybierz klienta') }}</flux:select.option>
                            @foreach($clients as $client)
                                <flux:select.option value="{{ $client->id }}">{{ $client->first_name }} {{ $client->last_name }} ({{ $client->email }})</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('client_id') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                    </div>
                @endif

                <!-- Urządzenie -->
                <div>
                    <flux:select wire:model="device_id" :label="__('Urządzenie')" :filter="false" :disabled="empty($devices)">
                        <flux:select.option value="">{{ __('Wybierz urządzenie') }}</flux:select.option>
                        @foreach($devices as $device)
                            <flux:select.option value="{{ $device->id }}">{{ $device->model }} ({{ $device->serial_number }})</flux:select.option>
                        @endforeach
                    </flux:select>
                    @error('device_id') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Technik -->
                <div>
                    <flux:select wire:model="user_id" :label="__('Technik')" :filter="false">
                        <flux:select.option value="">{{ __('Wybierz technika') }}</flux:select.option>
                        @foreach($technicians as $technician)
                            <flux:select.option value="{{ $technician->id }}">{{ $technician->first_name }} {{ $technician->last_name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    @error('user_id') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Data serwisu -->
                <flux:input
                    wire:model="service_date"
                    :label="__('Data serwisu')"
                    type="datetime-local"
                    value="{{ $service_date_formatted }}"
                />
                @error('service_date') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror

                <!-- Status -->
                <div>
                    <flux:select wire:model="status" :label="__('Status')" :filter="false">
                        <flux:select.option value="reported">{{ __('Zgłoszone') }}</flux:select.option>
                        <flux:select.option value="planned">{{ __('Zaplanowane') }}</flux:select.option>
                        <flux:select.option value="in_progress">{{ __('W trakcie') }}</flux:select.option>
                        <flux:select.option value="completed">{{ __('Zakończone') }}</flux:select.option>
                        <flux:select.option value="failed">{{ __('Nieudane') }}</flux:select.option>
                    </flux:select>
                    @error('status') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
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
                            placeholder="Opisz szczegółowo problem z urządzeniem..."></textarea>
                    </div>
                    @error('description') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Zaplanuj automatycznie w kalendarzu -->
                <div class="mt-4">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model="schedule_automatically"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-900 dark:border-gray-700"
                        >
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Zaplanuj automatycznie w kalendarzu') }}</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Zaznacz, aby automatycznie utworzyć wydarzenie w kalendarzu dla wybranego technika') }}
                    </p>
                </div>

                <!-- Przyciski formularza -->
                <div class="flex items-center justify-end gap-4 pt-4">
                    <flux:link :href="route('services.index')">
                        {{ __('Anuluj') }}
                    </flux:link>
                    <flux:button type="submit">
                        {{ __('Dodaj zgłoszenie') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </x-services.layout>
</section>
