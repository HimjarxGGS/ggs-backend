<?php

namespace App\Filament\Resources\PendaftarEventResource\Pages;

use App\Filament\Resources\PendaftarEventResource;
use App\Mail\PendaftarVerifiedMail;
use App\Models\PendaftarEvent;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Facades\Filament;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class DetailPendaftarEvent extends EditRecord 
{


    protected static string $resource = PendaftarEventResource::class;
    // local property bound to the form field (optional, but explicit)
    public ?string $status = null;

    protected static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('pendaftar');
    }

    public function update(User $user, PendaftarEvent $event)
    {
        return $user->isAdmin(); // or your own condition
    }

    public function form(Form $form): Form
    {
        // dd($this->record);
        return $form
            ->schema([
                Section::make('Data Pendaftar')
                    ->schema([
                        Placeholder::make('pendaftar.nama_lengkap')
                            ->label('Nama Lengkap')
                            ->content(fn($record) => $record->pendaftar->nama_lengkap),

                        Placeholder::make('pendaftar.email')
                            ->label('Email')
                            ->content(fn($record) => $record->pendaftar->email),

                        Placeholder::make('pendaftar.age')
                            ->label('Usia')
                            ->content(fn($record) => $record->pendaftar->age),

                        Placeholder::make('pendaftar.no_telepon')
                            ->label('Nomor Telepon')
                            ->content(fn($record) => $record->pendaftar->no_telepon),

                        Placeholder::make('pendaftar.asal_instansi')
                            ->label('Asal Instansi')
                            ->content(fn($record) => $record->pendaftar->asal_instansi),

                        Placeholder::make('pendaftar.riwayat_penyakit')
                            ->label('Riwayat Penyakit')
                            ->content(fn($record) => $record->pendaftar->riwayat_penyakit),

                        Placeholder::make('kesediaan_hadir')
                            ->label('Ketersediaan Hadir Mengikuti Kegiatan Pada Hari H')
                            ->content(fn($record) => $record->kesediaan_hadir),

                        Placeholder::make('kesediaan_menaati_aturan')
                            ->label('Ketersediaan Menaati Segala Tata Tertib Yang Berlaku')
                            ->content(fn($record) => $record->kesediaan_menaati_aturan),

                        Placeholder::make('opsi_payment')
                            ->label('Opsi Pembayaran Yang Dipilih')
                            ->content(fn($record) => $record->opsi_payment),

                        Select::make('status')
                            ->label('Status Verifikasi')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state === 'approved') {
                                    $this->record->approved_by = Filament::auth()->user()->id;
                                } else {
                                    $this->record->approved_by = null;
                                }
                            })
                            ->required(),

                        TextInput::make('approved_by')->default(Filament::auth()->user()->id)->hidden(),

                    ])
                    ->columns(2),


                Section::make('Bukti')
                    ->schema([
                        Placeholder::make('bukti_share_poster')
                            ->label('Bukti Share Poster')
                            ->content(function ($record) {
                                if (! $record->bukti_share_poster) {
                                    return 'Bukti Share Poster tidak ditemukan.';
                                }

                                $url = $this->resolveImageUrl($record->bukti_share_poster);

                                return new HtmlString("<img src='{$url}' alt='Bukti Share Poster' style='max-height:300px; border-radius: 8px;'/>");
                            }), // IMPORTANT: to render HTML

                        Placeholder::make('bukti_pembayaran')
                            ->label('Bukti Pembayaran')
                            ->content(function ($record) {
                                if (! $record->bukti_pembayaran) {
                                    return 'Bukti share poster tidak ditemukan.';
                                }

                                $url = $this->resolveImageUrl($record->bukti_pembayaran);

                                return new HtmlString("<img src='{$url}' alt='Bukti Share Poster' style='max-height:300px; border-radius: 8px;'/>");
                            }),

                        Placeholder::make('registrant_picture')
                            ->label('Foto Pendaftar')
                            ->content(function ($record) {
                                if (! $record->pendaftar->registrant_picture) {
                                    return 'Foto pendaftar tidak ditemukan.';
                                }

                                $url = $this->resolveImageUrl($record->pendaftar->registrant_picture);

                                return new HtmlString(
                                    "<img src='{$url}' alt='Bukti Share Poster' style='max-height:300px; border-radius: 8px;'/>"
                                );
                                // return "<img src='{$url}' alt='Bukti Share Poster' style='max-height:300px; border-radius: 8px;'/>";
                            }),

                    ])
                    ->columns(3),

            ])
            ->columns(1);
    }

    function resolveImageUrl(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        // If already a full URL, just return it
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // Otherwise treat it as a storage path
        return Storage::url($value);
    }

    // persist the change server-side
    public function saveStatus(?string $newStatus): void
    {
        /** @var PendaftarEvent $record */
        $record = $this->record;

        if (! $record) {
            $this->notify('danger', 'Record not found.');
            return;
        }

        $record->status = $newStatus;

        if ($newStatus === 'approved') {
            $record->approved_by =  Filament::auth()->user()->id;
        } else {
            $record->approved_by = null; // or keep existing — pick what you need
        }

        $record->save();

        // keep UI in sync
        $this->form->fill(['status' => $record->status]);
        $this->record->refresh();

        $this->notify('success', "Status updated to: {$newStatus}");
    }

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.resources.pendaftar-events.index') => 'Event',
            route('filament.admin.resources.pendaftar-events.index', ['event_id' => $this->record->event_id]) => 'Data Pendaftar',
            route('filament.admin.resources.pendaftar-events.detail-pendaftar', ['record' => $this->record->id]) => 'Detail',
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

    public function getTitle(): string
    {
        return 'Detail Pendaftar';
    }
}
