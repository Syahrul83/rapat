<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Daftar Hadir - {{ $meeting->title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f3f4f6;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .info {
            margin-bottom: 20px;
        }

        .info p {
            margin: 4px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>DAFTAR HADIR RAPAT</h2>
    </div>

    <div class="info">
        <p><strong>Kegiatan:</strong> {{ $meeting->title }}</p>
        <p><strong>Hari/Tanggal:</strong>
            {{ $meeting->meetingDays->pluck('date')->map(fn($d) => $d->format('d M Y'))->implode(', ') }}</p>
        <p><strong>Jam:</strong> {{ $meeting->start_time }} - {{ $meeting->end_time }}</p>
        <p><strong>Lokasi:</strong> {{ $meeting->location }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="150">Nama</th>
                <th width="80">Jenis</th>
                <th width="80">Tipe</th>
                <th width="120">NIP/NIK</th>
                <th width="80">Tgl Daftar</th>
                <th width="100">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($participants as $index => $participant)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $participant->name }}</td>
                    <td>{{ $participant->jenis_peserta === 'pegawai_dinas' ? 'ASN' : 'Eksternal' }}</td>
                    <td>{{ ucfirst($participant->tipe_peserta) }}</td>
                    <td>{{ $participant->nip ?? $participant->nik }}</td>
                    <td>{{ $participant->registered_at ? $participant->registered_at->format('d/m/Y') : '-' }}</td>
                    <td>
                        @if (strlen($participant->signature_data ?? '') > 500)
                            <img src="{{ $participant->signature_data }}" alt="TTD"
                                style="height: 30px; width: auto;">
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- <table style="margin-top: 50px; width: 100%; border: none;">
        <tr>
            <td style="width: 50%; vertical-align: top; border: none;">
                <p>Mengetahui,</p>
            </td>
            <td style="width: 50%; vertical-align: top; text-align: right; border: none;">
                <p>{{ \Carbon\Carbon::now()->format('d M Y') }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 60px; border: none;">
                <div style="width: 150px; border-bottom: 1px solid #000; margin-bottom: 4px;">&nbsp;</div>
                <p>(___________________)</p>
            </td>
            <td style="padding-top: 60px; text-align: right; border: none;">
                <div style="width: 150px; border-bottom: 1px solid #000; margin-bottom: 4px; margin-left: auto;">&nbsp;</div>
                <p>(___________________)</p>
            </td>
        </tr>
        <tr>
            <td style="border: none;"></td>
            <td style="text-align: right; padding-top: 4px; border: none;">
                <p>Ketua Rapat</p>
            </td>
        </tr>
    </table> --}}
</body>

</html>
