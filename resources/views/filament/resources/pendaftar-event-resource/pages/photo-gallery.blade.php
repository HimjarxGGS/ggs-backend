<x-filament::page>

    {{-- Gallery grid --}}
    <p>Search : {{ $this->search }}</p>
    <x-filament::grid
        style="--cols-lg: repeat(3, minmax(0, 1fr));"
        class="lg:grid-cols-[--cols-lg]">
        @forelse ($this->getRegistrants() as $pendaftar)
        <div class="max-w-sm bg-orange-50 rounded-lg shadow-lg  justify-center grid">
            <img class="w-full h-48 object-cover"
                src="{{ $pendaftar->registrant_picture}}"
                alt="Event Image">
            <span class="flex p-2 w-full">
                <div class="p-2 text-sm font-medium w-full">
                    {{ $pendaftar->nama_lengkap }}
                </div>

                <div class="p-2 text-sm font-medium">
                    <a href="" style="color:orange">Download</a>
                </div>

            </span>
        </div>

        @empty
        <div class="col-span-4 text-center text-gray-500 py-10">
            Tidak ada foto yang cocok dengan "{{ $this->search }}"
        </div>
        @endforelse
    </x-filament::grid>
</x-filament::page>