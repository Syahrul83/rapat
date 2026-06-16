<?php

use Livewire\Component;
use App\Models\Meeting;

new class extends Component {
    public Meeting $meeting;

    public function mount($id)
    {
        $this->meeting = Meeting::with(['meetingDays', 'participants', 'creator'])->findOrFail($id);
    }
};
?>

<div>
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-6">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">Jadwal Rapat</a>
                    <a href="{{ route('admin.meetings') }}" class="text-sm text-blue-600 font-semibold">Rapat</a>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Detail Rapat</h1>
            <div class="space-x-2">
                <a href="{{ route('admin.meetings.edit', $this->meeting) }}"
                    class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">Edit</a>
                <a href="{{ route('admin.meetings.pdf', $this->meeting) }}"
                    target="_blank"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Cetak PDF</a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Judul</h3>
                    <p class="text-lg">{{ $this->meeting->title }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Status</h3>
                    @if($this->meeting->status->value === 'published')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Lokasi</h3>
                    <p>{{ $this->meeting->location }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Jam</h3>
                    <p>{{ $this->meeting->start_time }} - {{ $this->meeting->end_time }}</p>
                </div>
                <div class="col-span-2">
                    <h3 class="text-sm font-medium text-gray-500">Tanggal</h3>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @foreach($this->meeting->meetingDays as $day)
                            <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-sm">
                                {{ $day->date->format('d M Y') }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @if($this->meeting->description)
                    <div class="col-span-2">
                        <h3 class="text-sm font-medium text-gray-500">Keterangan</h3>
                        <p>{{ $this->meeting->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold">Daftar Peserta ({{ $this->meeting->participants->count() }})</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIP/NIK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Daftar</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($this->meeting->participants as $index => $participant)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $participant->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $participant->jenis_peserta === 'pegawai_dinas' ? 'Pegawai Dinas' : 'Eksternal' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ ucfirst($participant->tipe_peserta) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $participant->nip ?? $participant->nik }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $participant->registered_at ? $participant->registered_at->format('d M Y H:i') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada peserta mendaftar</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>
