<?php

namespace App\Filament\Resources\PendaftarEventResource\Pages;

use App\Filament\Resources\PendaftarEventResource;
use App\Mail\PendaftarVerifiedMail;
use App\Models\PendaftarEvent;
use App\Models\User;
use App\Traits\HasSimpleNotify;
use Filament\Forms\Components\Select;
use Filament\Facades\Filament;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

class DetailPendaftarEvent extends EditRecord
{
    use HasSimpleNotify;
    protected static string $resource = PendaftarEventResource::class;
    // local property bound to the form field (optional, but explicit)
    // public ?string $status = null;

    // setting the template for email subject and message 
    // (it should can be customized from external config, but that's a prblem for future me (Seta) )
    public $email_subject = 'Verifikasi Pendaftaran Event Green Generation Surabaya';
    public $email_message = 'Selamat Pendaftaran Anda Telah Di Verifikasi';

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
                                'verified' => 'Verified',
                            ])
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                // delegate to a page method 
                                $this->handleStatusChanged($state);
                            })
                            ->required(),


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

                                $url = $record->pendaftar->registrant_picture_url; //$this->resolveImageUrl($record->pendaftar->regisr);

                                return new HtmlString(
                                    "<img src='{$url}' alt='Bukti Share Poster' style='max-height:300px; border-radius: 8px;'/>"
                                );
                            }),

                    ])
                    ->columns(3),

                // Email subform: only visible when status === 'approved'
                Section::make('Template Pesan Email')
                    ->schema([
                        TextInput::make('email_subject')
                            ->label('Subject')
                            ->required()
                            ->dehydrated(false)
                            ->default('Verifikasi Pendaftaran')->required()
                            ->afterStateHydrated(function (TextInput $component) {
                                // if ($component->getState() === '') {
                                $component->state($this->email_subject);
                                // }
                            })->afterStateUpdated(function ($state) {
                                $this->email_subject = $state;
                            }),

                        RichEditor::make('email_message')
                            ->label('Pesan')
                            ->default("Pendaftaran Sudah Diverifikasi")
                            ->dehydrated(false)
                            ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList', 'code', 'insertImage'])
                            ->required()
                            ->afterStateHydrated(function (RichEditor $component) {
                                // if ($component->getState() === '') {
                                $component->state($this->email_message);
                                // }
                            })->afterStateUpdated(function ($state) {
                                $this->email_message = $state;
                            }),

                        View::make('filament.resources.components.email-send-button')->columnSpanFull(),

                    ])
                    // visibility uses the form state getter $get()
                    ->visible(fn(callable $get) => $get('status') === 'verified')
                    ->columns(1),

            ])
            ->columns(1);
    }

    /**
     * Called when status changes in the Select (immediately).
     * Persist the change and set approved_by if necessary.
     */
    public function handleStatusChanged(?string $newStatus): void
    {
        if (! $this->record) {
            $this->notify('danger', 'Record not loaded.');
            return;
        }

        if ($newStatus !== 'pending' && $newStatus !== 'verified') {
            return;
        }

        // Save atomically
        $this->record->status = $newStatus;

        if ($newStatus === 'verified') {
            $this->record->approved_by = Filament::auth()->user()->id;
        } else {
            $this->record->approved_by = null;
        }

        $this->record->save();

        // Keep the form state and record in sync
        // (filament form instance should reflect change, but be explicit)

        $this->form->fill([
            'status' => $this->record->status,
            'email_subject' => $this->email_subject,
            'email_message' => $this->email_message
        ]);

        $this->record->refresh();

        $this->notify('success', "Status diubah menjadi: {$newStatus}");
    }

    // function resolveImageUrl(?string $value): ?string
    // {
    //     if (! $value) {
    //         return null;
    //     }

    //     // If already a full URL, just return it
    //     if (filter_var($value, FILTER_VALIDATE_URL)) {
    //         return $value;
    //     }

    //     // Otherwise treat it as a storage path
    //     return Storage::url($value);
    // }

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.resources.pendaftar-events.index') => 'Event',
            route('filament.admin.resources.pendaftar-events.index', ['event_id' => $this->record->event_id]) => 'Data Pendaftar',
            route('filament.admin.resources.pendaftar-events.detail-pendaftar', ['record' => $this->record->id]) => 'Detail',
        ];
    }

    /**
     * Wire method called by the inline "Kirim Verifikasi" button.
     * Reads form state (subject & message) and calls mailer.
     */
    public function submitEmail(): void
    {
        $subject = $this->email_subject; //$state['email_subject']; //?? 'Verifikasi Pendaftaran';
        $message = $this->email_message; //$state['email_message']; //?? '';

        // Basic validation (optional)
        if (! $this->record->pendaftar?->email) {
            $this->notify('danger', 'Registrant has no email.');
            return;
        }

        try {
            
            Mail::to($this->record->pendaftar->email)->send(new PendaftarVerifiedMail(
                $this->record,
                $subject,
                $message
            ));

            $this->notify('success', 'Email terkirim ke ' . $this->record->pendaftar->email);
        } catch (\Throwable $e) {
            // log and notify
            Log::error('Failed to send verification email', ['err' => $e->getMessage()]);
            $this->notify('danger', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }

    public function getTitle(): string
    {
        return 'Detail Pendaftar';
    }
}
