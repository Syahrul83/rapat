<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Notulensi</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11pt;
            margin: 20px;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header-table {
            width: 100%;
        }

        .logo {
            width: 130px;
            text-align: center;
        }

        .logo img {
            width: 120px;
            height: auto;
        }

        .instansi {
            text-align: center;
        }

        .instansi h3,
        .instansi h4,
        .instansi p {
            margin: 0;
        }

        .judul {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 15px 0;
        }

        .identitas {
            width: 100%;
            margin-bottom: 15px;
        }

        .identitas td {
            padding: 3px;
            vertical-align: top;
        }

        .isi {
            min-height: 150px;
            text-align: justify;
            line-height: 1.5;
            text-justify: inter-word;
        }

        .foto {
            text-align: center;
            margin-top: 20px;
        }

        .foto img {
            width: 300px;
            height: auto;
        }

        .ttd {
            width: 100%;
            margin-top: 40px;
        }

        .ttd td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .spasi-ttd {
            height: 80px;
        }

        .alamat {
            font-size: 11px;
            margin: 0;
        }
    </style>
</head>

<body>

    <div class="header">
        <table class="header-table">
            <tr>
                <td class="logo">
                    <img src="{{ public_path('image/lambang.png') }}">
                </td>

                <td class="instansi">
                    <h3>BADAN KARANTINA INDONESIA</h3>
                    <h4>BALAI BESAR KARANTINA HEWAN IKAN DAN TUMBUHAN</h4>
                    <h4>KALIMANTAN TIMUR</h4>

                    <p class="alamat">Jl. Pelita No. 3 Sepinggan Balikpapan 76115</p>
                    <p class="alamat">TELEPON : (0542) 8523292 FAKSIMILI : (0542) 413650</p>
                    <p class="alamat">email : karantinakaltim@karantinaindonesia.go.id</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="judul">
        NOTULENSI
    </div>

    <table class="identitas">
        <tr>
            <td width="180">Nama Kegiatan</td>
            <td width="10">:</td>
            <td>{{ $notulensi->meeting->title }}</td>
        </tr>

        <tr>
            <td>Tanggal Kegiatan</td>
            <td>:</td>
            <td>{{ $notulensi->meeting_date->format('d M Y') }}</td>
        </tr>

        <tr>
            <td>Lokasi Kegiatan</td>
            <td>:</td>
            <td>{{ $notulensi->location }}</td>
        </tr>

        <tr>
            <td>Isi Notulensi </td>
            <td>:</td>
            <td> </td>
        </tr>
    </table>
    <div class="isi">
        {{ $notulensi->isi_notulensi }}
    </div>

    <table class="ttd">
        <tr>
            <td>
                Notulen
                @if ($notulensi->notulen->ttd_image == null)
                    <div class="spasi-ttd"></div>
                @endif
                @if ($notulensi->notulen->ttd_image)
                    <br>
                    <img width="150" src="{{ public_path('storage/' . $notulensi->notulen->ttd_image) }}"
                        alt="TTD Notulen" class="ttd-img">
                    <br>
                @endif

                <strong>{{ $notulensi->notulen->name }}</strong><br>
                NIP. {{ $notulensi->notulen->nip }}
            </td>

            <td>
                Kepala Balai Besar Karantina<br>
                Hewan, Ikan dan Tumbuhan<br>
                Kalimantan Timur
                @if ($notulensi->kepala->ttd_image == null)
                    <div class="spasi-ttd"></div>
                @endif


                @if ($notulensi->kepala->ttd_image)
                    <br>
                    <img width="150" src="{{ public_path('storage/' . $notulensi->kepala->ttd_image) }}"
                        alt="TTD Kepala" class="ttd-img">
                    <br>
                @endif
                <strong>{{ $notulensi->kepala->name }}</strong><br>
                NIP. {{ $notulensi->kepala->nip }}
            </td>
        </tr>
    </table>

    <div style="text-align: center; font-weight: bold; page-break-before: always;"> Foto Dokumentasi </div><br>
    <br>
    @if ($notulensi->photos->count() > 0)

        <div class="foto">
            @foreach ($notulensi->photos as $photo)
                <img src="{{ public_path('storage/' . $photo->photo_path) }}" alt="Foto">
            @endforeach
        </div>
    @endif
</body>

</html>
