<section class="w-full">
    @include('partials.services-heading')
    <x-services.layout :heading="__('Zgłoś serwis')" :subheading="__('Utwórz nowe zgłoszenie serwisowe')">
        <div class="my-6 w-full">
            @if (session()->has('message'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('message') }}</p>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <form wire:submit.prevent="reportService" class="space-y-6">
                <!-- Urządzenie -->
                <div>
                    <flux:select wire:model="device_id" :label="__('Urządzenie')" :filter="false">
                        <flux:select.option value="">{{ __('Wybierz urządzenie') }}</flux:select.option>
                        @foreach($devices as $device)
                            <flux:select.option value="{{ $device->id }}">{{ $device->model }} ({{ $device->serial_number }})</flux:select.option>
                        @endforeach
                    </flux:select>
                    @error('device_id') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Opis problemu -->
                <div>
                    <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Opis problemu') }}</label>
                    <div class="mt-1">
                        <flux:textarea
                            wire:model="description"
                            rows="5"
                            placeholder="Opisz szczegółowo problem z urządzeniem..."
                        />
                    </div>
                    @error('description') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Przyciski formularza -->
                <div class="flex items-center justify-end gap-4 pt-4">
                    <flux:link :href="route('services.index')">
                        {{ __('Anuluj') }}
                    </flux:link>
                    <flux:button type="submit">
                        {{ __('Zgłoś serwis') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </x-services.layout>
</section>
