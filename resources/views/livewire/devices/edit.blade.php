<section class="w-full">
    @include('partials.devices-heading')
    <x-devices.layout :heading="__('Edycja urządzenia')" :subheading="__('Edytuj szczegóły urządzenia')">
        <div class="my-6 w-full">
            @if (session()->has('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <form wire:submit.prevent="updateDevice" class="space-y-6">
                <!-- Klient (dla admina/pracownika) -->
                @if(auth()->check() && auth()->user()->hasRole(['Admin', 'employee']))
                    <div>
                        <flux:select wire:model="client_id" :label="__('Klient')" :filter="false">
                            <flux:select.option value="">{{ __('Wybierz klienta') }}</flux:select.option>
                            @foreach($clients as $client)
                                <flux:select.option value="{{ $client->id }}">{{ $client->first_name }} {{ $client->last_name }} ({{ $client->email }})</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('client_id') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                    </div>
                @endif

                <!-- Model urządzenia -->
                <flux:input wire:model="model" :label="__('Model urządzenia')" type="text" />
                @error('model') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror

                <!-- Numer seryjny z przyciskiem skanowania -->
                <div>
                    <label for="serial_number" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Numer seryjny</label>
                    <div class="mt-1 flex">
                        <flux:input wire:model="serial_number" type="text" />
                        <flux:button type="button" wire:click="toggleQrScanner" class="ml-2">
                            {{ __('Skanuj kod') }}
                        </flux:button>
                    </div>
                    @error('serial_number') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Numer producenta -->
                <flux:input wire:model="producent_number" :label="__('Numer producenta')" type="text" />
                @error('producent_number') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror

                <!-- Data zakupu -->
                <flux:input wire:model="purchase_date" :label="__('Data zakupu')" type="date" />
                @error('purchase_date') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror

                <!-- Data końca gwarancji -->
                <flux:input wire:model="warranty_end_date" :label="__('Data końca gwarancji')" type="date" />
                @error('warranty_end_date') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror

                <!-- Przyciski formularza -->
                <div class="flex items-center justify-end gap-4 pt-4">
                    <flux:link :href="route('devices.index')">
                        {{ __('Anuluj') }}
                    </flux:link>
                    <flux:button type="submit">
                        {{ __('Zapisz zmiany') }}
                    </flux:button>
                </div>
            </form>
        </div>

        <!-- Modal skanera QR -->
        @if($showQrScanner)
            <flux:modal name="qr-scanner-modal" :show="true" focusable class="max-w-lg">
                <flux:heading size="lg">{{ __('Skanowanie kodu QR/kreskowego') }}</flux:heading>

                <div id="scanner-container" class="mb-4 h-64 bg-gray-100 dark:bg-gray-700 relative mt-4">
                    <div id="reader" class="w-full h-full"></div>
                </div>

                <div class="text-center text-sm text-gray-600 dark:text-gray-400 mb-4">
                    {{ __('Umieść kod QR lub kod kreskowy w polu widzenia kamery.') }}
                </div>

                <div class="flex justify-end">
                    <flux:modal.close>
                        <flux:button wire:click="toggleQrScanner">
                            {{ __('Zamknij') }}
                        </flux:button>
                    </flux:modal.close>
                </div>

                <script>
                    document.addEventListener('livewire:initialized', function () {
                        function startScanner() {
                            const html5QrCode = new Html5Qrcode("reader");
                            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

                            html5QrCode.start(
                                { facingMode: "environment" },
                                config,
                                (decodedText) => {
                                    // Przesyłanie odczytanego kodu do komponentu
                                    @this.handleScannedCode(decodedText);
                                    html5QrCode.stop();
                                },
                                (errorMessage) => {
                                    // Pomiń błędy, to normalne podczas skanowania
                                }
                            ).catch((err) => {
                                console.error("Error starting scanner:", err);
                            });
                        }

                        // Uruchom skaner po załadowaniu modala
                        if (document.getElementById("reader")) {
                            setTimeout(startScanner, 500);
                        }
                    });
                </script>
            </flux:modal>
        @endif
    </x-devices.layout>
</section>
