<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin-bottom: 4px; }
        .header p { margin-top: 0; color: #555; font-size: 11px; }
        .filter-info { margin-bottom: 15px; font-size: 11px; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN REKAPITULASI</h2>
        <p>PANRB Digital Services</p>
    </div>

    <div class="filter-info">
        <strong>Tipe Laporan:</strong>
        {{ $filterType === 'kegiatan' ? 'Per Kegiatan' : ($filterType === 'bulanan' ? 'Per Bulan' : 'Per Tahun') }}
        @if($filterType !== 'kegiatan')
            | <strong>Tahun:</strong> {{ $filterYear }}
        @endif
        @if($filterType === 'bulanan' && $filterMonth)
            | <strong>Bulan:</strong> {{ \Carbon\Carbon::createFromDate($filterYear, $filterMonth, 1)->translatedFormat('F') }}
        @endif
        @if($filterType === 'kegiatan' && $filterMeeting)
            | <strong>Rapat:</strong> {{ \App\Models\Meeting::find($filterMeeting)?->title ?? '-' }}
        @endif
    </div>

    @if($filterType === 'kegiatan')
        <table>
            <thead>
                <tr>
                    <th width="30">No</th>
                    <th>Kegiatan</th>
                    <th>Tanggal</th>
                    <th>Lokasi</th>
                    <th width="50" class="text-center">Total</th>
                    <th width="70" class="text-center">Narasumber</th>
                    <th width="60" class="text-center">Peserta</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report as $index => $row)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $row['title'] }}</td>
                        <td>{{ $row['dates'] }}</td>
                        <td>{{ $row['location'] }}</td>
                        <td class="text-center">{{ $row['total_participants'] }}</td>
                        <td class="text-center">{{ $row['narasumber_count'] }}</td>
                        <td class="text-center">{{ $row['peserta_count'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <table>
            <thead>
                <tr>
                    <th width="30">No</th>
                    <th>{{ $filterType === 'bulanan' ? 'Bulan' : 'Tahun' }}</th>
                    <th class="text-center">Jumlah Rapat</th>
                    <th class="text-center">Jumlah Peserta</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report as $index => $row)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            {{ $filterType === 'bulanan' ? ($row['nama_bulan'] ?? '') . ' ' . $filterYear : $row['tahun'] ?? $filterYear }}
                        </td>
                        <td class="text-center">{{ $row['jumlah_rapat'] }}</td>
                        <td class="text-center">{{ $row['jumlah_peserta'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <p style="margin-top: 30px; font-size: 10px; color: #888; text-align: center;">
        Dicetak pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WITA
    </p>
</body>
</html>
