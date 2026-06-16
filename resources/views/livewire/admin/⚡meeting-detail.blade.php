<?php

use Livewire\Component;
use App\Models\Meeting;

new class extends Component {
    public Meeting $meeting;
    public $filterDate = '';

    public function mount($id)
    {
        $this->meeting = Meeting::with(['meetingDays', 'creator'])->findOrFail($id);
    }

    public function getFilteredParticipants()
    {
        $query = $this->meeting->participants();

        if ($this->filterDate) {
            $query->whereDate('registered_at', $this->filterDate);
        }

        return $query->orderBy('registered_at')->get();
    }

    public function getAvailableDates()
    {
        return $this->meeting->participants()
            ->select('registered_at')
            ->whereNotNull('registered_at')
            ->distinct()
            ->orderBy('registered_at')
            ->get()
            ->map(fn($p) => $p->registered_at->format('Y-m-d'))
            ->unique()
            ->values()
            ->toArray();
    }

    public function getParticipantCount()
    {
        return $this->getFilteredParticipants()->count();
    }

    public function getTotalCount()
    {
        return $this->meeting->participants()->count();
    }
};
?>

<div>
    <a href="{{ route('admin.meetings') }}" class="text-label-md text-primary hover:underline mb-4 inline-flex items-center gap-1">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
    </a>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-headline-md text-primary">Detail Rapat</h1>
        <div class="flex gap-3">
            <a href="{{ route('admin.meetings.edit', $this->meeting) }}" class="px-4 py-2 bg-primary text-on-primary rounded-lg text-label-md font-bold hover:brightness-95 transition-all">Edit</a>
            <a href="{{ $this->filterDate ? route('admin.meetings.pdf', [$this->meeting, 'date' => $this->filterDate]) : route('admin.meetings.pdf', $this->meeting) }}" target="_blank" class="px-4 py-2 bg-error text-on-error rounded-lg text-label-md font-bold hover:brightness-95 transition-all">Cetak PDF</a>
        </div>
    </div>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 mb-6">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-label-sm text-outline mb-1">Judul</p>
                <p class="text-body-md text-primary font-semibold">{{ $this->meeting->title }}</p>
            </div>
            <div>
                <p class="text-label-sm text-outline mb-1">Status</p>
                @if($this->meeting->status->value === 'published')
                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-label-sm font-bold">Published</span>
                @else
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-label-sm font-bold">Draft</span>
                @endif
            </div>
            <div>
                <p class="text-label-sm text-outline mb-1">Lokasi</p>
                <p class="text-body-md text-secondary">{{ $this->meeting->location }}</p>
            </div>
            <div>
                <p class="text-label-sm text-outline mb-1">Jam</p>
                <p class="text-body-md text-secondary">{{ $this->meeting->start_time }} - {{ $this->meeting->end_time }}</p>
            </div>
            <div class="col-span-2">
                <p class="text-label-sm text-outline mb-1">Tanggal</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($this->meeting->meetingDays as $day)
                        <span class="px-3 py-1 rounded-full bg-primary-container text-on-primary-container text-label-sm">{{ $day->date->format('d M Y') }}</span>
                    @endforeach
                </div>
            </div>
            @if($this->meeting->description)
                <div class="col-span-2">
                    <p class="text-label-sm text-outline mb-1">Keterangan</p>
                    <p class="text-body-md text-secondary">{{ $this->meeting->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-outline-variant flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-headline-md text-primary">Daftar Peserta ({{ $this->getParticipantCount() }}/{{ $this->getTotalCount() }})</h2>
            <div class="flex items-center gap-2">
                <label class="text-label-sm text-on-surface-variant">Filter Tanggal:</label>
                <input type="date" wire:model.live="filterDate" class="px-3 py-1.5 bg-surface-bright border border-outline-variant rounded-lg text-body-md focus:ring-2 focus:ring-primary outline-none">
                @if($this->filterDate)
                    <button wire:click="$set('filterDate', '')" class="text-label-sm text-primary hover:underline">Reset</button>
                @endif
            </div>
        </div>
        @php $participants = $this->getFilteredParticipants(); @endphp
        <table class="w-full text-left">
            <thead class="bg-surface-container-low text-primary text-label-md border-b border-outline-variant">
                <tr>
                    <th class="px-4 py-4">No</th>
                    <th class="px-4 py-4">Nama</th>
                    <th class="px-4 py-4">Jenis</th>
                    <th class="px-4 py-4">Tipe</th>
                    <th class="px-4 py-4">NIP/NIK</th>
                    <th class="px-4 py-4">Tanggal Daftar</th>
                    <th class="px-4 py-4">TTD</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($participants as $index => $participant)
                    <tr class="hover:bg-surface-container-low transition-colors">
                        <td class="px-4 py-4 text-body-md text-on-surface-variant">{{ $index + 1 }}</td>
                        <td class="px-4 py-4 text-body-md text-primary font-semibold">{{ $participant->name }}</td>
                        <td class="px-4 py-4 text-body-md text-secondary">{{ $participant->jenis_peserta === 'pegawai_dinas' ? 'Pegawai Dinas' : 'Eksternal' }}</td>
                        <td class="px-4 py-4 text-body-md text-secondary">{{ ucfirst($participant->tipe_peserta) }}</td>
                        <td class="px-4 py-4 text-body-md text-secondary">{{ $participant->nip ?? $participant->nik }}</td>
                        <td class="px-4 py-4 text-body-md text-secondary">{{ $participant->registered_at ? $participant->registered_at->format('d M Y H:i') : '-' }}</td>
                        <td class="px-4 py-4">
                            @if(strlen($participant->signature_data ?? '') > 500)
                                <img src="{{ $participant->signature_data }}" alt="TTD" class="h-10 w-auto border border-outline-variant rounded">
                            @else
                                <span class="text-label-sm text-secondary">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-secondary text-body-md">Belum ada peserta mendaftar</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
