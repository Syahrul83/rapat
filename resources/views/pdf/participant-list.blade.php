<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Hadir - {{ $meeting->title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; }
        .header { text-align: center; margin-bottom: 20px; }
        .info { margin-bottom: 20px; }
        .info p { margin: 4px 0; }
        .signature-box { margin-top: 40px; display: inline-block; width: 150px; border-bottom: 1px solid #000; }
    </style>
</head>
<body>
    <div class="header">
        <h2>DAFTAR HADIR RAPAT</h2>
    </div>

    <div class="info">
        <p><strong>Kegiatan:</strong> {{ $meeting->title }}</p>
        <p><strong>Hari/Tanggal:</strong> {{ $meeting->meetingDays->pluck('date')->map(fn($d) => $d->format('d M Y'))->implode(', ') }}</p>
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
                <th width="100">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($meeting->participants as $index => $participant)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $participant->name }}</td>
                    <td>{{ $participant->jenis_peserta === 'pegawai_dinas' ? 'Pegawai' : 'Eksternal' }}</td>
                    <td>{{ ucfirst($participant->tipe_peserta) }}</td>
                    <td>{{ $participant->nip ?? $participant->nik }}</td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 50px; display: flex; justify-content: space-between;">
        <div>
            <p>Mengetahui,</p>
            <div class="signature-box"></div>
            <p>(___________________)</p>
        </div>
        <div>
            <p>{{ \Carbon\Carbon::now()->format('d M Y') }}</p>
            <p>Ketua Rapat</p>
            <div class="signature-box"></div>
            <p>(___________________)</p>
        </div>
    </div>
</body>
</html>
