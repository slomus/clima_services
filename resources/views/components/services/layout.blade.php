<div class="flex items-start max-md:flex-col">
    <div class="mr-10 w-full pb-4 md:w-[220px]">
        <flux:navlist>
            <flux:navlist.item :href="route('services.index')" wire:navigate>{{ __('Lista') }}</flux:navlist.item>

            @if(auth()->check())
                @if(auth()->user()->hasRole('Client'))
                    {{-- Show "Zgłoś" for clients --}}
                    <flux:navlist.item :href="route('services.report')" wire:navigate>{{ __('Zgłoś') }}</flux:navlist.item>
                @else
                    {{-- Show "Dodaj" for admins and technicians --}}
                    <flux:navlist.item :href="route('services.create')" wire:navigate>{{ __('Dodaj') }}</flux:navlist.item>

                    {{-- Show "Zgłoszenia" for admins and technicians --}}
                    @if(auth()->user()->can('tickets.approve'))
                        <flux:navlist.item :href="route('services.pending-approvals')" wire:navigate>{{ __('Zgłoszenia') }}</flux:navlist.item>
                    @endif
                @endif
            @endif
        </flux:navlist>
    </div>
    <flux:separator class="md:hidden" />
    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>
        <div class="mt-5 w-full">
            {{ $slot }}
        </div>
    </div>
</div>
