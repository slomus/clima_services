<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <flux:heading>{{ __('Usuń konto') }}</flux:heading>
        <flux:subheading>{{ __('Usuń swoje konto i wszystkie jego zasoby') }}</flux:subheading>
    </div>

    <flux:modal.trigger name="confirm-user-deletion">
        <flux:button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        {{ __('Delete account') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
        <form wire:submit="deleteUser" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Czy na pewno chcesz usunąć swoje konto?') }}</flux:heading>

                <flux:subheading>
                    {{ __('Po usunięciu konta wszystkie jego zasoby i dane zostaną na stałe usunięte. Wprowadź hasło, aby potwierdzić, że chcesz na stałe usunąć swoje konto.') }}
                </flux:subheading>
            </div>

            <flux:input wire:model="password" :label="__('Hasło')" type="password" />

            <div class="flex justify-end space-x-2">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" type="submit">{{ __('Usuń konto') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
