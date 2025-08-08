<div class="flex items-center gap-3">
    <form class="flex items-center gap-2" onsubmit="return false;">
        <input
            type="search"
            id="filament-search-inline"
            placeholder="Cari nama atau nama file..."
            class="filament-toolbar-input rounded px-3 py-2 w-80"
            aria-label="Search registrants"
            oninput="Livewire.dispatch('searchUpdated', [this.value])"
        />

        <button
            type="button"
            onclick="Livewire.dispatch('searchUpdated', '')"
            class="inline-flex items-center gap-2 px-3 py-2 rounded border"
        >
            Clear
        </button>
    </form>
</div>