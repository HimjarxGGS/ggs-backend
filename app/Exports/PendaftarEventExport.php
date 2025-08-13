<?php

namespace App\Exports;

use App\Models\PendaftarEvent;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PendaftarEventExport implements FromView
{
    public function __construct(public int $eventId) {}

    public function view(): View
    {
        $registrants = PendaftarEvent::with(['pendaftar', 'approvedBy'])
            ->where('event_id', $this->eventId)
            ->get();

        return view('exports.pendaftar_events', [
            'registrants' => $registrants,
        ]);
    }
}
