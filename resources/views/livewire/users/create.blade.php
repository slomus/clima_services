<section class="w-full">
    @include('partials.users-heading')

    <x-users.layout :heading="__('Tworzenie użytkownika')" :subheading="__('Stwórz nowe konto użytkownika')">
        <div class="my-6 w-full">
            @if (session()->has('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <form wire:submit.prevent="createUser" class="space-y-6">
                <!-- E-mail -->
                <flux:input 
                    wire:model="email" 
                    :label="__('E-mail')" 
                    type="email" 
                    autofocus 
                    autocomplete="email" 
                />
                @error('email') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror

                <!-- Rola/Uprawnienia -->
                <div>
                    <flux:select 
                        wire:model="role" 
                        :label="__('Uprawnienia')" 
                        :filter="false" 
                        id="role" 
                        name="role"
                    >
                        <flux:select.option value="">{{ __('Wybierz') }}</flux:select.option>
                        @foreach($roles as $role)
                            <flux:select.option value="{{ $role }}">{{ $role }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    @error('role') <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Przyciski formularza -->
                <div class="flex items-center justify-end gap-4 pt-4">
                    <flux:link :href="route('users.index')">
                        {{ __('Anuluj') }}
                    </flux:link>
                    <flux:button type="submit">
                        {{ __('Dodaj użytkownika') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Zapisano.') }}
                </x-action-message>
            </form>
        </div>
    </x-users.layout>
</section>