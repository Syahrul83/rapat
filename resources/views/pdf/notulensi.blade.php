<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Notulensi - {{ $notulensi->meeting->title }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin-bottom: 4px; }
        .header .sub { font-size: 11px; color: #666; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 4px 8px; vertical-align: top; }
        .info-table .label { width: 130px; font-weight: bold; }
        .content { margin-bottom: 30px; line-height: 1.6; text-align: justify; }
        .photos { margin-bottom: 30px; }
        .photos img { max-height: 200px; max-width: 100%; margin: 8px; }
        .signature-table { width: 100%; border: none; margin-top: 40px; }
        .signature-table td { border: none; vertical-align: top; width: 50%; }
        .signature-box { text-align: center; }
        .signature-box .ttd-img { height: 50px; margin-bottom: 4px; }
        .signature-box .name { font-weight: bold; margin-top: 4px; }
        .signature-box .title { font-size: 11px; color: #666; }
        .signature-box .line { border-bottom: 1px solid #000; width: 200px; margin: 4px auto; }
    </style>
</head>
<body>
    <div class="header">
        <h2>NOTULENSI RAPAT</h2>
        <p class="sub">Meeting Digital Service</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Kegiatan</td>
            <td>: {{ $notulensi->meeting->title }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Kegiatan</td>
            <td>: {{ $notulensi->meeting_date->format('d M Y') }}</td>
        </tr>
        <tr>
            <td class="label">Lokasi</td>
            <td>: {{ $notulensi->location }}</td>
        </tr>
    </table>

    <h3>Isi Notulensi</h3>
    <div class="content">
        {{ $notulensi->isi_notulensi }}
    </div>

    @if($notulensi->photos->count() > 0)
        <h3>Dokumentasi</h3>
        <div class="photos">
            @foreach($notulensi->photos as $photo)
                <img src="{{ public_path('storage/' . $photo->photo_path) }}" alt="Foto">
            @endforeach
        </div>
    @endif

    <table class="signature-table">
        <tr>
            <td class="signature-box">
                <p>Notulen</p>
                <br>
                @if($notulensi->notulen->ttd_image)
                    <img src="{{ public_path('storage/' . $notulensi->notulen->ttd_image) }}" alt="TTD Notulen" class="ttd-img">
                @endif
                <div class="line">&nbsp;</div>
                <p class="name">{{ $notulensi->notulen->name }}</p>
                <p class="title">NIP. {{ $notulensi->notulen->nip }}</p>
            </td>
            <td class="signature-box">
                <p>Mengetahui,</p>
                <p>{{ $notulensi->meeting_date->format('d M Y') }}</p>
                <br>
                @if($notulensi->kepala->ttd_image)
                    <img src="{{ public_path('storage/' . $notulensi->kepala->ttd_image) }}" alt="TTD Kepala" class="ttd-img">
                @endif
                <div class="line">&nbsp;</div>
                <p class="name">{{ $notulensi->kepala->name }}</p>
                <p class="title">NIP. {{ $notulensi->kepala->nip }}</p>
            </td>
        </tr>
    </table>
</body>
</html>
