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
                            <td class="px-4 py-4 text-right">
                                Akcje
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
    </x-users.layout>
</section>
