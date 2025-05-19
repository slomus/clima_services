<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Konto')" :subheading="__('Zaktualizuj dane konta')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="first_name" :label="__('Imię')" type="text" autofocus autocomplete="firstName" />
            <flux:input wire:model="last_name" :label="__('Nazwisko')" type="text" autocomplete="lastName" />
            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" autocomplete="email" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>
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
            </div>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Zapisano.') }}
            </x-action-message>

        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
