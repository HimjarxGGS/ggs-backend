<x-filament::page>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h2 class="text-2xl font-bold">Foto Pendaftar: {{ $this->event->name }}</h2>
            <div class="flex space-x-2">
                {{-- Search bar --}}
                <x-filament::input
                    placeholder="Cari Foto…"
                    wire:model.debounce.500ms="search"
                    icon="heroicon-o-search"
                />

                {{-- Download all button --}}
                <x-filament::button
                    wire:click="downloadAll"
                    icon="heroicon-o-arrow-down-tray"
                    color="success"
                >
                    Download Semua
                </x-filament::button>
            </div>
        </div>
    </x-slot>

    {{-- Gallery grid --}}
    <div class="grid grid-cols-4 gap-6 mt-4">
        @forelse ($this->getRegistrants() as $pendaftar)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <img
                    src="{{ Storage::url($pendaftar->registrant_picture) }}"
                    class="w-full h-48 object-cover"
                    alt="{{ $pendaftar->nama_lengkap }}"
                >
                <div class="p-2 text-sm font-medium">
                    {{ $pendaftar->nama_lengkap }}
                </div>
            </div>
        @empty
            <div class="col-span-4 text-center text-gray-500 py-10">
                Tidak ada foto yang cocok dengan "{{ $this->search }}"
            </div>
        @endforelse
    </div>
</x-filament::page>
