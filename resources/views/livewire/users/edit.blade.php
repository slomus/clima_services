<section class="w-full">
    @include('partials.users-heading')

    <x-users.layout :heading="__('Konto użytkownika')" :subheading="__('Zaktualizuj dane konta użytkownika')">
        <form wire:submit="updateUserInformtion" class="my-6 w-full space-y-6">
            <flux:input wire:model="first_name" :label="__('Imię')" type="text" autofocus autocomplete="firstName" />
            <flux:input wire:model="last_name" :label="__('Nazwisko')" type="text" autocomplete="lastName" />
            <flux:input wire:model="email" :label="__('Email')" type="email" autocomplete="email" />
            <flux:input wire:model="phone" :label="__('Nr.telefonu')" type="text" autocapitalize="phoneNumber" />
            <flux:select wire:model="address_city_id" :label="__('Miasto')" :filter="false" id="address_city_id" name="address_city_id">
            <flux:select.option value="Wybierz">{{ __('Wybierz') }}</flux:select.option>
                @foreach($cities as $city)
                    @if($address_city_id === $city['id'])
                        <flux:select.option selected value="{{ $city['id'] }}">{{ $city['name'] }}</flux:select.option>
                    @else
                        <flux:select.option value="{{ $city['id'] }}">{{ $city['name'] }}</flux:select.option>
                    @endif
                @endforeach
            </flux:select>
            <flux:input wire:model="address_street" :label="__('Ulica')" type="text" />
            <flux:input wire:model="address_home_number" :label="__('Nr.domu')" type="text" />
            <flux:input wire:model="address_apartment_number" :label="__('Nr.mieszkania')" type="text" />
            <flux:input wire:model="address_post_code" mask="99-999" :label="__('Kod pocztowy')" type="text" />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Zapisz') }}</flux:button>
                </div>
                <flux:link wire:wire:navigate href="{{ route('users.index') }}">
                    <flux:button variant="filled" class="w-full">{{ __('Anuluj') }}</flux:button>
                </flux:link>
            </div>

            <x-action-message class="me-3" on="user-updated">
                {{ __('Zaktualizowano.') }}
            </x-action-message>

        </form>
    </x-users.layout>
</section>
