<section class="w-full">
    @include('partials.users-heading')

    <x-users.layout :heading="__('Tworzenie użytkownika')" :subheading="__('Stwórz nowe konto użytkownika')">
        <form wire:submit="createUser" class="my-6 w-full space-y-6">
            <flux:input wire:model="email" :label="__('E-mail')" type="email" autofocus autocomplete="email" />

            <flux:select wire:model="role" :label="__('Uprawnienia')" :filter="false" id="role" name="role">
                <flux:select.option value="Wybierz">{{ __('Wybierz') }}</flux:select.option>
                @foreach($roles as $role)
                    <flux:select.option value="{{ $role }}">{{ $role }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Zapisz') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Zapisano.') }}
                </x-action-message>
            </div>
        </form>
    </x-users.layout>
</section>
