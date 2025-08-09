<?php

namespace App\Filament\Resources\PendaftarEventResource\Pages;

use App\Filament\Resources\PendaftarEventResource;
use App\Mail\PendaftarVerifiedMail;
use App\Models\PendaftarEvent;
use Filament\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class ViewPendaftarEvent extends ViewRecord implements HasInfolists
{
    use InteractsWithInfolists;

    protected static string $resource = PendaftarEventResource::class;

    protected static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('pendaftar');
    }

public function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->record($this->record->load('pendaftar'))
        ->schema([
            Section::make('Data Pendaftar')
                ->schema([
                    TextEntry::make('pendaftar.nama_lengkap')
                        ->label('Nama Lengkap'),

                    TextEntry::make('pendaftar.email')
                        ->label('Email'),

                    TextEntry::make('pendaftar.nomor_telepon')
                        ->label('Nomor Telepon'),

                    TextEntry::make('pendaftar.asal_instansi')
                        ->label('Asal Instansi'),

                    TextEntry::make('pendaftar.riwayat_penyakit')
                        ->label('Riwayat Penyakit'),
                ])
                ->columns(2),

            Section::make('Bukti')
                ->schema([
                    ImageEntry::make('bukti_pembayaran')
                        ->label('Bukti Pembayaran')
                        ->disk('public') // adjust if using different disk
                        ->url(fn ($record) => $record->bukti_pembayaran
                            ? Storage::url($record->bukti_pembayaran)
                            : null
                        ),

                    ImageEntry::make('bukti_share_poster')
                        ->label('Bukti Share Poster')
                        ->disk('public')
                        ->url(fn ($record) => $record->bukti_share_poster
                            ? Storage::url($record->bukti_share_poster)
                            : null
                        ),

                    ImageEntry::make('pendaftar.registrant_picture')
                        ->label('Foto Pendaftar')
                        ->disk('public')
                        ->url(fn ($record) => $record->pendaftar?->registrant_picture
                            ? Storage::url($record->pendaftar->registrant_picture)
                            : null
                        ),
                ])
                ->columns(3),

            // Section::make('Status Verifikasi')
            //     ->schema([
            //         SelectEn::make('status')
            //             ->label('Status Validasi Pembayaran')
            //             ->options([
            //                 'pending' => 'Pending',
            //                 'verified' => 'Verified',
            //                 'rejected' => 'Rejected',
            //             ]),
            //     ]),
        ]);
}

    /**
     * Autosave handler invoked when status changes.
     * What: persist new status, notify admin, and (if verified) make email form visible.
     * Why: keep the save server-side, auditable, and trigger any further logic.
     */
    public function saveStatus(string $newStatus): void
    {
        // ensure we have the record model
        /** @var PendaftarEvent $record */
        $record = $this->record;

        $record->status = $newStatus;
        $record->save();

        // Flash / notify admin in Filament
        $this->notify('success', 'Status berhasil disimpan.');

        // If verified, optionally fire an event or set a UI flag
        // In this simple approach, the page will re-render and the email form will show if verified
    }

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.resources.pendaftar-events.index') => 'Event',
            route('filament.admin.resources.pendaftar-events.index', ['event_id' => $this->record->event_id]) => 'Data Pendaftar',
            route('filament.admin.resources.pendaftar-events.view', ['record' => $this->record->id]) => 'Detail',
        ];
    }

    // 2) Email sending method (invoked by the button on the email subform)
    public function sendVerificationEmail(array $data = []): void
    {
        $email = $this->record->pendaftar->email;
        if (! $email) {
            $this->notify('danger', 'Registrant has no email.');
            return;
        }

        // $data should contain 'subject' and 'message'
        Mail::to($email)->send(new PendaftarVerifiedMail(
            $this->record,
            $data['subject'] ?? 'Verifikasi Pembayaran',
            $data['message'] ?? 'Selamat, pembayaran Anda sudah diverifikasi.'
        ));

        $this->notify('success', 'Email terkirim ke ' . $email);
    }
}
