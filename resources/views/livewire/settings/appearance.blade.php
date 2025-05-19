<div class="flex flex-col items-start">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Wygląd')" :subheading=" __('Zaktualizuj ustawienia wyglądu dla swojego konta')">
        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun">{{ __('Jasny') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('Ciemny') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ __('Systemowy') }}</flux:radio>
        </flux:radio.group>
    </x-settings.layout>
</div>
