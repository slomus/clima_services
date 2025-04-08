<section class="w-full px-4 sm:px-6 lg:px-8">
    @include('partials.users-heading')

    <x-users.layout :heading="__('Lista użytkowników')" :subheading="__('Wszyscy użytkownicy w systemie')">
        <!-- Wyszukiwanie i filtrowanie -->
        <div class="flex flex-col sm:flex-row justify-between mb-4 gap-4">
            <!-- Wyszukiwanie -->
            <flux:input 
                type="text" 
                wire:model.live.debounce.100ms="search" 
                placeholder="Szukaj użytkownika..." 
                class="w-full sm:w-auto rounded-md px-4 py-2 border border-gray-300 focus:outline-none focus:ring focus:border-blue-500 dark:border-gray-700 dark:text-gray-200"
            />

            <!-- Filtrowanie po roli -->
            <flux:select 
                wire:model.live="roleFilter" 
                placeholder="Wybierz rolę" 
                class="w-full sm:w-auto rounded-md px-4 py-2 border border-gray-300 focus:outline-none focus:ring focus:border-blue-500 dark:border-gray-700 dark:text-gray-200"
            >
                <flux:select.option value="">Wybierz rolę</flux:select.option>
                @foreach($roles as $role)
                    <flux:select.option value="{{ $role }}">{{ $role }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <!-- Tabela użytkowników -->
        @canany(['users.view', 'clients.view'])
            <div class="overflow-x-auto border shadow-md rounded-lg">
                <table class="w-full table-auto divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="sticky top-0 z-[1]">
                        <tr>
                            @foreach($headers as $key => $value)
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-gray-300">
                                    @if($key != 'actions' && $key != 'roles')
                                        <flux:button wire:click="sortBy('{{ $key }}')" class="group inline-flex items-center">
                                            {{ $value }}
                                            @if($sortField === $key)
                                                <span class="ml-1">
                                                    @if($sortDirection === 'asc')
                                                        ↑
                                                    @else
                                                        ↓
                                                    @endif
                                                </span>
                                            @endif
                                        </flux:button>
                                    @else
                                        {{ $value }}
                                    @endif
                                    
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="border divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($users as $user)
                            <tr class="border hover:bg-gray-100 dark:hover:bg-gray-600">
                                <td class="px-4 py-4 sm:table-cell">
                                    {{ $user->id }}
                                </td>
                                <td class="px-4 py-4">
                                    {{ $user->first_name }}
                                </td>
                                <td class="px-4 py-4">
                                    {{ $user->last_name }}
                                </td>
                                <td class="px-4 py-4 break-words">
                                    {{ $user->email }}
                                </td>
                                <td class="px-4 py-4 break-words">
                                    {{ implode(', ', $user->roles->pluck('name')->toArray()) }}
                                </td>
                                <td class="px-4 py-4 flex items-center gap-2">
                                    @canany(['users.edit', 'clients.edit'])
                                        <flux:link wire:navigate href="{{ route('users.edit', $user->id) }}">
                                            <flux:icon.pencil class="text-blue-600 hover:text-blue-100 dark:hover:text-blue-400"/>
                                        </flux:link>
                                    @endcanany

                                    @if(auth()->user()->hasRole('Admin') && (!$user->hasRole('Admin') || auth()->id() !== $user->id))
                                        @canany(['users.delete','clients.delete'])
                                            <flux:modal.trigger name="confirm-user-account-deletion">
                                                <flux:icon.trash wire:click="confirmDelete({{ $user->id }})" class="text-red-600 hover:text-red-100 dark:hover:text-red-400"/>
                                            </flux:modal.trigger>
                                        @endcanany
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 px-4 text-center text-gray-400">
                                    Brak użytkowników spełniających kryteria wyszukiwania.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Modal potwierdzenia -->
            <flux:modal name="confirm-user-account-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
                <form wire:submit.prevent="deleteUser" class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('Jesteś pewien, że chcesz usunąć konto?') }}</flux:heading>

                        <flux:subheading>
                            {{ __('Po usunięciu konta wszystkie jego zasoby i dane zostaną na stałe usunięte. Wprowadź hasło, aby potwierdzić, że chcesz na stałe usunąć swoje konto.') }}
                        </flux:subheading>
                    </div>

                    <flux:input wire:model="password" :label="__('Hasło')" type="password" />

                    <div class="flex justify-end space-x-2">
                        <flux:modal.close>
                            <flux:button variant="filled">{{ __('Anuluj') }}</flux:button>
                        </flux:modal.close>

                        <flux:button variant="danger" type="submit">{{ __('Usuń konto') }}</flux:button>
                    </div>
                </form>
            </flux:modal>
        @endcanany
    </x-users.layout>
</section>
