<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Zaktualizuj hasło')" :subheading="__('Upewnij się, że Twoje konto używa długiego, losowego hasła, aby zachować bezpieczeństwo')">
        <form wire:submit="updatePassword" class="mt-6 space-y-6">
            <flux:input
                wire:model="current_password"
                :label="__('Atkaulne hasło')"
                type="password"
                autocomplete="current-password"
            />
            <flux:input
                wire:model="password"
                :label="__('Nowe hasło')"
                type="password"
                autocomplete="new-password"
            />
            <flux:input
                wire:model="password_confirmation"
                :label="__('Powtórz nowe hasło')"
                type="password"
                autocomplete="new-password"
            />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Zapisz') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="password-updated">
                    {{ __('Zapisano') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>
