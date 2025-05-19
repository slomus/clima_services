<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Utwórz konto')" :description="__('Wprowadź swoje dane poniżej, aby utworzyć konto')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        
        <flux:input wire:model="first_name" :label="__('Imię')" type="text" autofocus autocomplete="firstName" :placeholder="__('Imię')" />
        <flux:input wire:model="last_name" :label="__('Nazwisko')" type="text" autocomplete="lastName" :placeholder="__('Nazwisko')" />
        <flux:input wire:model="email" :label="__('E-mail')" type="email" autocomplete="email" placeholder="email@example.com" />
        <flux:input wire:model="phone" :label="__('Nr.telefonu')" type="text" autocapitalize="phoneNumber" :placeholder="__('Nr.telefonu')" />
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
        <flux:input wire:model="address_street" :label="__('Ulica')" type="text" :placeholder="__('Ulica')" />
        <flux:input wire:model="address_home_number" :label="__('Nr.domu')" type="text" :placeholder="__('Nr.domu')" />
        <flux:input wire:model="address_apartment_number" :label="__('Nr.mieszkania')" type="text" :placeholder="__('Nr.mieszkania')" />
        <flux:input wire:model="address_post_code" mask="99-999" :label="__('Kod pocztowy')" type="text" :placeholder="__('Kod pocztowy')" />
        <flux:input wire:model="password" :label="__('Hasło')" type="password" autocomplete="new-password" :placeholder="__('Password')" />
        <flux:input wire:model="password_confirmation" :label="__('Powtórz hasło')" type="password" autocomplete="new-password" :placeholder="__('Confirm password')" />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Utwórz konto') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Masz już konto?') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('Zaloguj się') }}</flux:link>
    </div>
</div>
