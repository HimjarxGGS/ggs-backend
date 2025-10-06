<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $pendaftar->name ?? 'Verifikasi Pendaftaran' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="margin:0; padding:0; background-color:#f4f4f4;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td bgcolor="#f4f4f4" align="center" style="padding: 20px 10px;">
                <!-- Main Container -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="max-width:600px; background-color:#ffffff; border-radius:8px; overflow:hidden;">
                    <!-- Header -->
                    <tr>
                        <td align="center" bgcolor="#4CAF50" style="padding: 20px;">
                            <h1 style="margin:0; font-size:24px; font-family:Arial, sans-serif; color:#ffffff;">
                                Verifikasi Pendaftaran Event Green Generation Surabaya
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td
                            style="padding: 30px; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #333333;">
                            <p>Halo <strong>{{ $pendaftar->pendaftar->nama_lengkap ?? 'Peserta' }}</strong>,</p>

                            {!! $messageBody !!}

                            <p>
                                Untuk informasi lebih lanjut mengenai acara ini, silakan kunjungi website resmi
                                kami:<br>
                                <a href="{{ config('app.url') }}" style="color:#4CAF50;">{{ config('app.url') }}</a><br><br>
                            </p>

                            <p>Salam hangat,<br>
                                <strong>Tim Penyelenggara Green Generation Surabaya</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td bgcolor="#f4f4f4" align="center" style="padding: 20px; font-size: 12px; font-family: Arial, sans-serif; color: #666666;">
                            Website: <a href="{{ config('app.url') }}" style="color:#4CAF50;">{{ config('app.url') }}</a><br><br>
                            Jika Anda menerima email ini karena kesalahan, mohon abaikan saja.
                        </td>
                    </tr>
                </table>
                <!-- End Main Container -->
            </td>
        </tr>
    </table>
</body>

</html>
