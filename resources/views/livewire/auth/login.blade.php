<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Zaloguj sie')" :description="__('Podaj e-mail i hasło aby się zalogować')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">

        <flux:input
            wire:model="email"
            :label="__('E-mail')"
            type="email"
            autofocus
            autocomplete="email"
            placeholder="email@example.com"
        />

        <div class="relative">
            <flux:input
                wire:model="password"
                :label="__('Hasło')"
                type="password"
                autocomplete="current-password"
                :placeholder="__('Password')"
            />

            @if (Route::has('password.request'))
                <flux:link class="absolute right-0 top-0 text-sm" :href="route('password.request')" wire:navigate>
                    {{ __('Zapomniałeś hasła?') }}
                </flux:link>
            @endif
        </div>

        <flux:checkbox wire:model="remember" :label="__('Pamiętaj mnie')" />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Zaloguj sie') }}</flux:button>
        </div>
    </form>
</div>
