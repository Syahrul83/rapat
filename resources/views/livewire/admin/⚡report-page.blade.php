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
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-6">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">Jadwal Rapat</a>
                    <a href="{{ route('admin.meetings') }}" class="text-sm text-gray-600 hover:text-gray-900">Rapat</a>
                    <a href="{{ route('admin.reports') }}" class="text-sm text-blue-600 font-semibold">Laporan</a>
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('admin.users') }}" class="text-sm text-gray-600 hover:text-gray-900">Users</a>
                    @endif
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold mb-6">Laporan Rekapitulasi</h1>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Laporan</label>
                    <select wire:model.live="filter_type" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <option value="kegiatan">Per Kegiatan</option>
                        <option value="bulanan">Per Bulan</option>
                        <option value="tahunan">Per Tahun</option>
                    </select>
                </div>

                @if($filter_type !== 'kegiatan')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                        <select wire:model.live="filter_year" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                            @foreach($this->getYears() as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if($filter_type === 'bulanan')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                        <select wire:model.live="filter_month" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option value="">Semua Bulan</option>
                            @foreach($this->getMonths() as $num => $name)
                                <option value="{{ $num }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if($filter_type === 'kegiatan')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rapat</label>
                        <select wire:model.live="filter_meeting" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option value="">Semua Rapat</option>
                            @foreach($this->getMeetings() as $meeting)
                                <option value="{{ $meeting->id }}">{{ $meeting->title }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if($filter_type === 'kegiatan')
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kegiatan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Narasumber</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Peserta</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($this->report() as $index => $row)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $row['title'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $row['dates'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $row['location'] }}</td>
                                <td class="px-6 py-4 text-sm text-center font-semibold text-blue-600">{{ $row['total_participants'] }}</td>
                                <td class="px-6 py-4 text-sm text-center text-green-600">{{ $row['narasumber_count'] }}</td>
                                <td class="px-6 py-4 text-sm text-center text-orange-600">{{ $row['peserta_count'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $filter_type === 'bulanan' ? 'Bulan' : 'Tahun' }}</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah Rapat</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah Peserta</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($this->report() as $index => $row)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $filter_type === 'bulanan' ? ($row['nama_bulan'] ?? '') . ' ' . $filter_year : $row['tahun'] ?? $filter_year }}
                                </td>
                                <td class="px-6 py-4 text-sm text-center font-semibold text-blue-600">{{ $row['jumlah_rapat'] }}</td>
                                <td class="px-6 py-4 text-sm text-center font-semibold text-green-600">{{ $row['jumlah_peserta'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </main>
</div>
