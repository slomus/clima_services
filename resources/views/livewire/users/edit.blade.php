<section class="w-full">
    @include('partials.users-heading')

    <x-users.layout :heading="__('Konto użytkownika')" :subheading="__('Zaktualizuj dane konta użytkownika')">
        <div class="my-6 w-full">
            @if (session()->has('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <form wire:submit.prevent="updateUserInformtion" class="space-y-6">
                <!-- Dane osobowe -->
                <flux:input wire:model="first_name" :label="__('Imię')" type="text" autofocus autocomplete="firstName" />
                @error('first_name') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror

                <flux:input wire:model="last_name" :label="__('Nazwisko')" type="text" autocomplete="lastName" />
                @error('last_name') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror

                <flux:input wire:model="email" :label="__('Email')" type="email" autocomplete="email" />
                @error('email') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror

                <flux:input wire:model="phone" :label="__('Nr.telefonu')" type="text" autocapitalize="phoneNumber" />
                @error('phone') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror

                <!-- Dane adresowe -->
                <div>
                    <flux:select 
                        wire:model="address_city_id" 
                        :label="__('Miasto')" 
                        :filter="false" 
                        id="address_city_id" 
                        name="address_city_id"
                    >
                        <flux:select.option value="">{{ __('Wybierz') }}</flux:select.option>
                        @foreach($cities as $city)
                            <flux:select.option value="{{ $city['id'] }}">{{ $city['name'] }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    @error('address_city_id') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                </div>

                <flux:input wire:model="address_street" :label="__('Ulica')" type="text" />
                @error('address_street') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror

                <flux:input wire:model="address_home_number" :label="__('Nr.domu')" type="text" />
                @error('address_home_number') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror

                <flux:input wire:model="address_apartment_number" :label="__('Nr.mieszkania')" type="text" />
                @error('address_apartment_number') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror

                <flux:input wire:model="address_post_code" mask="99-999" :label="__('Kod pocztowy')" type="text" />
                @error('address_post_code') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror

                <!-- Przyciski formularza -->
                <div class="flex items-center justify-end gap-4 pt-4">
                    <flux:link :href="route('users.index')">
                        {{ __('Anuluj') }}
                    </flux:link>
                    <flux:button type="submit">
                        {{ __('Zapisz zmiany') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="user-updated">
                    {{ __('Zaktualizowano.') }}
                </x-action-message>
            </form>
        </div>
    </x-users.layout>
</section>