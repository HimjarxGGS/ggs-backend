@php
    $record = $getRecord();
    $path = $record?->pendaftar?->registrant_picture ?: 'images/dummy.png';
    $url = asset('storage/' . ltrim($path, '/'));
@endphp

<div style="text-align:center; padding:.5rem 0;">
    <img src="{{ $url }}" alt="Foto Member" style="max-width:200px; border-radius:8px;">
</div>

{{-- <div class="flex justify-center">
    <img src="{{ $getRecord()->pendaftar?->registrant_picture 
        ? asset('storage/' . $getRecord()->pendaftar->registrant_picture) 
        : asset('storage/images/dummy.png') }}"
        alt="Foto Member"
        style="max-width:200px; border-radius:8px;">
</div> --}}