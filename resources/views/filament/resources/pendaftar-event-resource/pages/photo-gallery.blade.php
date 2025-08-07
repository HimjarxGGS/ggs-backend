<x-filament::page>
    <x-slot name="header">
        <h2>Gallery: {{ $this->event->name }}</h2>
    </x-slot>

    <div class="grid grid-cols-4 gap-4">
        @foreach ($this->getRegistrants() as $pendaftar)
            @if ($pendaftar->registrant_picture)
                <div class="rounded-lg overflow-hidden shadow-lg">
                    <img src="{{ Storage::url($pendaftar->registrant_picture) }}" class="w-full h-48 object-cover">
                    <div class="p-2 text-sm">
                        {{ $pendaftar->nama_lengkap }}
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</x-filament::page>
