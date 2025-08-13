<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Lengkap</th>
            <th>Email</th>
            <th>No Telepon</th>
            <th>Status</th>
            <th>Tanda Tangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($registrants as $index => $reg)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $reg->pendaftar->nama_lengkap }}</td>
                <td>{{ $reg->pendaftar->email }}</td>
                <td>{{ $reg->pendaftar->no_telepon }}</td>
                <td>{{ ucfirst($reg->status) }}</td>
                <td></td> {{-- empty cell for presence signature --}}
            </tr>
        @endforeach
    </tbody>
</table>
