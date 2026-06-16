<?php

use Livewire\Component;
use App\Services\ReportService;

new class extends Component {
    public $filter_type = 'kegiatan';
    public $filter_year = '';
    public $filter_month = '';
    public $filter_meeting = '';

    public function mount()
    {
        $this->filter_year = date('Y');
        $this->filter_month = date('n');
    }

    public function updatedFilterType()
    {
        $this->resetFilters();
    }

    public function resetFilters()
    {
        $this->filter_year = date('Y');
        $this->filter_month = date('n');
        $this->filter_meeting = '';
    }

    public function report()
    {
        $service = app(ReportService::class);

        return match ($this->filter_type) {
            'kegiatan' => $service->getPerKegiatan(
                $this->filter_meeting ? (int) $this->filter_meeting : null
            ),
            'bulanan' => $service->getPerBulan(
                (int) $this->filter_year,
                $this->filter_month ? (int) $this->filter_month : null
            ),
            'tahunan' => [$service->getPerTahun((int) $this->filter_year)],
            default => [],
        };
    }

    public function getMeetings()
    {
        return \App\Models\Meeting::where('status', 'published')->orderByDesc('created_at')->get();
    }

    public function getYears()
    {
        return range(date('Y') - 2, date('Y') + 1);
    }

    public function getMonths()
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
    }
};
?>

<div>
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-headline-md text-primary mb-1">Laporan Rekapitulasi</h1>
            <p class="text-body-md text-secondary">Pantau statistik rapat dan peserta secara berkala.</p>
        </div>
        <a href="{{ route('admin.reports.pdf', [
            'type' => $this->filter_type,
            'year' => $this->filter_year,
            'month' => $this->filter_month,
            'meeting' => $this->filter_meeting,
        ]) }}" target="_blank" class="px-4 py-2 bg-error text-on-error rounded-lg text-label-md font-bold hover:brightness-95 transition-all flex items-center gap-1">
            <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span> Cetak PDF
        </a>
    </div>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 mb-6">
        <div class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-label-sm text-on-surface mb-1">Tipe Laporan</label>
                <select wire:model.live="filter_type" class="px-3 py-2 bg-surface-bright border border-outline-variant rounded-lg text-body-md focus:ring-2 focus:ring-primary outline-none">
                    <option value="kegiatan">Per Kegiatan</option>
                    <option value="bulanan">Per Bulan</option>
                    <option value="tahunan">Per Tahun</option>
                </select>
            </div>

            @if($filter_type !== 'kegiatan')
                <div>
                    <label class="block text-label-sm text-on-surface mb-1">Tahun</label>
                    <select wire:model.live="filter_year" class="px-3 py-2 bg-surface-bright border border-outline-variant rounded-lg text-body-md focus:ring-2 focus:ring-primary outline-none">
                        @foreach($this->getYears() as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if($filter_type === 'bulanan')
                <div>
                    <label class="block text-label-sm text-on-surface mb-1">Bulan</label>
                    <select wire:model.live="filter_month" class="px-3 py-2 bg-surface-bright border border-outline-variant rounded-lg text-body-md focus:ring-2 focus:ring-primary outline-none">
                        <option value="">Semua Bulan</option>
                        @foreach($this->getMonths() as $num => $name)
                            <option value="{{ $num }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if($filter_type === 'kegiatan')
                <div>
                    <label class="block text-label-sm text-on-surface mb-1">Rapat</label>
                    <select wire:model.live="filter_meeting" class="px-3 py-2 bg-surface-bright border border-outline-variant rounded-lg text-body-md focus:ring-2 focus:ring-primary outline-none">
                        <option value="">Semua Rapat</option>
                        @foreach($this->getMeetings() as $meeting)
                            <option value="{{ $meeting->id }}">{{ $meeting->title }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden">
        @if($filter_type === 'kegiatan')
            <table class="w-full text-left">
                <thead class="bg-surface-container-low text-primary text-label-md border-b border-outline-variant">
                    <tr>
                        <th class="px-4 py-4">No</th>
                        <th class="px-4 py-4">Kegiatan</th>
                        <th class="px-4 py-4">Tanggal</th>
                        <th class="px-4 py-4">Lokasi</th>
                        <th class="px-4 py-4 text-center">Total</th>
                        <th class="px-4 py-4 text-center">Narasumber</th>
                        <th class="px-4 py-4 text-center">Peserta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse($this->report() as $index => $row)
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="px-4 py-4 text-body-md text-on-surface-variant">{{ $index + 1 }}</td>
                            <td class="px-4 py-4 text-body-md text-primary font-semibold">{{ $row['title'] }}</td>
                            <td class="px-4 py-4 text-body-md text-secondary">{{ $row['dates'] }}</td>
                            <td class="px-4 py-4 text-body-md text-secondary">{{ $row['location'] }}</td>
                            <td class="px-4 py-4 text-body-md text-center font-bold text-primary">{{ $row['total_participants'] }}</td>
                            <td class="px-4 py-4 text-body-md text-center text-green-600">{{ $row['narasumber_count'] }}</td>
                            <td class="px-4 py-4 text-body-md text-center text-secondary">{{ $row['peserta_count'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-secondary text-body-md">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @else
            <table class="w-full text-left">
                <thead class="bg-surface-container-low text-primary text-label-md border-b border-outline-variant">
                    <tr>
                        <th class="px-4 py-4">No</th>
                        <th class="px-4 py-4">{{ $filter_type === 'bulanan' ? 'Bulan' : 'Tahun' }}</th>
                        <th class="px-4 py-4 text-center">Jumlah Rapat</th>
                        <th class="px-4 py-4 text-center">Jumlah Peserta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse($this->report() as $index => $row)
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="px-4 py-4 text-body-md text-on-surface-variant">{{ $index + 1 }}</td>
                            <td class="px-4 py-4 text-body-md text-primary font-semibold">
                                {{ $filter_type === 'bulanan' ? ($row['nama_bulan'] ?? '') . ' ' . $filter_year : $row['tahun'] ?? $filter_year }}
                            </td>
                            <td class="px-4 py-4 text-body-md text-center font-bold text-primary">{{ $row['jumlah_rapat'] }}</td>
                            <td class="px-4 py-4 text-body-md text-center font-bold text-green-600">{{ $row['jumlah_peserta'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-secondary text-body-md">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>
</div>
