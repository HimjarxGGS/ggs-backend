<div class="flex items-center gap-3">
    <form class="flex items-center gap-2" onsubmit="return false;">
        <x-filament::input.wrapper suffix-icon="heroicon-o-magnifying-glass">
            <x-filament::input
                type="search"
                id="filament-search-inline"
                placeholder="Cari foto"
                class="w-full"
                aria-label="Search registrants"
                oninput="Livewire.dispatch('searchUpdated', [this.value])" />
        </x-filament::input.wrapper>

    </form>
</div>