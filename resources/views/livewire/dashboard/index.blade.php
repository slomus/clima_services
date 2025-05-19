<section class="w-full px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
        <flux:subheading size="lg">{{ __('Podsumowanie systemu i kalendarz serwisów') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 flex flex-col items-center">
            <flux:icon.users class="text-blue-500 mb-2" size="lg" />
            <div class="text-3xl font-bold">{{ $clientsCount }}</div>
            <div class="text-sm text-gray-500">{{ __('Liczba klientów') }}</div>
        </div>
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 flex flex-col items-center">
            <flux:icon.wrench class="text-green-500 mb-2" size="lg" />
            <div class="text-3xl font-bold">{{ $openServicesCount }}</div>
            <div class="text-sm text-gray-500">{{ __('Otwarte zgłoszenia serwisowe') }}</div>
        </div>
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 flex flex-col items-center">
            <flux:icon.clipboard-document-list class="text-yellow-500 mb-2" size="lg" />
            <div class="text-3xl font-bold">{{ $devicesCount }}</div>
            <div class="text-sm text-gray-500">{{ __('Liczba urządzeń') }}</div>
        </div>
    </div>

    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6">
        <flux:heading size="lg" class="mb-4">{{ __('Kalendarz serwisów') }}</flux:heading>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-600 dark:text-gray-300">{{ __('Data') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-600 dark:text-gray-300">{{ __('Urządzenie') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-600 dark:text-gray-300">{{ __('Technik') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-600 dark:text-gray-300">{{ __('Status') }}</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($calendarEvents as $event)
                        <tr>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($event['service_date'])->format('d.m.Y H:i') }}</td>
                            <td class="px-4 py-2">{{ $event['device'] }}</td>
                            <td class="px-4 py-2">{{ $event['technician'] }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-xs
                                    @if($event['status'] === 'completed') bg-green-100 text-green-800
                                    @elseif($event['status'] === 'in_progress') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ __($event['status_label']) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 flex gap-2">
                                <flux:button size="xs" variant="primary" as="a" href="{{ route('services.edit', $event['id']) }}">
                                    {{ __('Edytuj') }}
                                </flux:button>
                                <flux:button size="xs" variant="danger" wire:click="deleteEvent({{ $event['id'] }})">{{ __('Usuń') }}</flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-400 py-6">{{ __('Brak zaplanowanych serwisów.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            <flux:button variant="primary" as="a" href="{{ route('services.create') }}">
                {{ __('Dodaj wydarzenie') }}
            </flux:button>
        </div>
    </div>
</section>
