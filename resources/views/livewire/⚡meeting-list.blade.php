<?php

use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Meeting;
use App\Models\Participant;

new class extends Component {
    public $search = '';
    public $showModal = false;
    public $selectedMeetingId = null;

    // Registration form fields
    #[Validate('required|in:pegawai_dinas,eksternal')]
    public $jenis_peserta = 'pegawai_dinas';

    #[Validate('required|in:narasumber,peserta')]
    public $tipe_peserta = 'peserta';

    #[Validate('required|max:255')]
    public $name = '';

    public $nip = '';
    public $nik = '';

    #[Validate('required')]
    public $signature_data = '';

    #[Validate('accepted')]
    public $declaration = false;

    public $registrationSuccess = false;

    public function meetings()
    {
        return Meeting::query()
            ->where('status', 'published')
            ->whereHas('meetingDays', function ($q) {
                $q->where('date', '>=', now()->subDay()->toDateString());
            })
            ->with('meetingDays')
            ->withCount('participants')
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->get()
            ->sortBy(fn($m) => $m->meetingDays->min('date'))
            ->values();
    }

    public function getDates($meeting)
    {
        return $meeting->meetingDays->sortBy('date')->pluck('date')->map(fn($d) => $d->format('d M Y'))->implode(', ');
    }

    public function openModal($meetingId)
    {
        $this->selectedMeetingId = $meetingId;
        $this->showModal = true;
        $this->resetForm();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedMeetingId = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->jenis_peserta = 'pegawai_dinas';
        $this->tipe_peserta = 'peserta';
        $this->name = '';
        $this->nip = '';
        $this->nik = '';
        $this->signature_data = '';
        $this->declaration = false;
        $this->registrationSuccess = false;
        $this->resetValidation();
    }

    public function getSelectedMeeting()
    {
        return $this->selectedMeetingId ? Meeting::with('meetingDays')->find($this->selectedMeetingId) : null;
    }

    public function updatedJenisPeserta()
    {
        $this->nip = '';
        $this->nik = '';
    }

    protected function rules()
    {
        return [
            'jenis_peserta' => 'required|in:pegawai_dinas,eksternal',
            'tipe_peserta' => 'required|in:narasumber,peserta',
            'name' => 'required|max:255',
            'nip' => $this->jenis_peserta === 'pegawai_dinas' ? 'required|digits_between:10,20' : 'nullable',
            'nik' => $this->jenis_peserta === 'eksternal' ? 'required|digits:16' : 'nullable',
            'signature_data' => 'required|min:100',
            'declaration' => 'accepted',
        ];
    }

    public function submitRegistration()
    {
        $this->validate();

        $exists = Participant::where('meeting_id', $this->selectedMeetingId)
            ->where(function ($q) {
                if ($this->jenis_peserta === 'pegawai_dinas') {
                    $q->where('nip', $this->nip);
                } else {
                    $q->where('nik', $this->nik);
                }
            })
            ->exists();

        if ($exists) {
            $this->addError('nip', 'Anda sudah terdaftar di rapat ini.');
            return;
        }

        Participant::create([
            'meeting_id' => $this->selectedMeetingId,
            'name' => $this->name,
            'jenis_peserta' => $this->jenis_peserta,
            'tipe_peserta' => $this->tipe_peserta,
            'nip' => $this->nip,
            'nik' => $this->nik,
            'signature_data' => $this->signature_data,
            'declaration' => $this->declaration,
            'registered_at' => now(),
        ]);

        $this->registrationSuccess = true;
    }
};
?>

<div>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Jadwal Rapat</h1>
                <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800">Login Admin</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari rapat..."
                class="w-full md:w-96 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($this->meetings() as $meeting)
                @php
                    $today = now()->toDateString();
                    $currentTime = now()->format('H:i');
                    $startTime = substr($meeting->start_time, 0, 5);
                    $endTime = substr($meeting->end_time, 0, 5);
                    $lastDay = $meeting->meetingDays->sortByDesc('date')->first();
                    $firstDay = $meeting->meetingDays->sortBy('date')->first();
                    $lastDate = $lastDay ? $lastDay->date->toDateString() : null;
                    $firstDate = $firstDay ? $firstDay->date->toDateString() : null;
                    $isAfterLastDay = $lastDate && $lastDate < $today;
                    $isLastDayToday = $lastDate === $today;
                    $meetingToday = $meeting->meetingDays->first(fn($d) => $d->date->toDateString() === $today);
                    $isInSession = $meetingToday && $currentTime >= $startTime && $currentTime <= $endTime;
                    if ($isAfterLastDay || ($isLastDayToday && $currentTime > $endTime)) {
                        $btnState = 'expired';
                        $btnLabel = 'Pendaftaran Ditutup';
                    } elseif ($isInSession) {
                        $btnState = 'open';
                        $btnLabel = 'Daftar';
                    } else {
                        $btnState = 'closed';
                        $btnLabel = 'Belum Dibuka';
                    }
                @endphp
                <div class="bg-white rounded-lg shadow-md overflow-hidden {{ $btnState === 'expired' ? 'opacity-50' : '' }}">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h2 class="text-lg font-semibold text-gray-800">{{ $meeting->title }}</h2>
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Published</span>
                        </div>
                        <div class="space-y-1 text-sm text-gray-600 mb-4">
                            <div><span class="font-medium">Hari/Tanggal:</span> {{ $this->getDates($meeting) }}</div>
                            <div><span class="font-medium">Jam:</span> {{ $meeting->start_time }} - {{ $meeting->end_time }}</div>
                            <div><span class="font-medium">Lokasi:</span> {{ $meeting->location }}</div>
                            @if($meeting->description)
                                <div><span class="font-medium">Keterangan:</span> {{ Str::limit($meeting->description, 80) }}</div>
                            @endif
                            <div><span class="font-medium">Peserta:</span> {{ $meeting->participants_count }} orang</div>
                        </div>
                        @if($btnState === 'open')
                            <button wire:click="openModal({{ $meeting->id }})" class="w-full py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">{{ $btnLabel }}</button>
                        @else
                            <button disabled class="w-full py-2 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed">{{ $btnLabel }}</button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-gray-400">Belum ada jadwal rapat saat ini</div>
            @endforelse
        </div>
    </main>

    @if($showModal && $selectedMeetingId)
        @php $meeting = $this->getSelectedMeeting(); @endphp
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50" wire:click="closeModal">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
                @if($registrationSuccess)
                    <div class="p-6 text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Pendaftaran Berhasil!</h3>
                        <p class="text-gray-600 mb-4">Anda terdaftar sebagai {{ $tipe_peserta === 'narasumber' ? 'Narasumber' : 'Peserta' }}.</p>
                        <button wire:click="closeModal" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Tutup</button>
                    </div>
                @else
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-gray-800">Daftar Rapat</h3>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">&times;</button>
                        </div>
                        @if($meeting)
                            <div class="bg-blue-50 rounded-md p-3 mb-4">
                                <p class="text-sm text-blue-800 font-medium">{{ $meeting->title }}</p>
                                <p class="text-xs text-blue-600">{{ $meeting->location }} | {{ $meeting->start_time }} - {{ $meeting->end_time }}</p>
                            </div>
                        @endif
                        <form wire:submit="submitRegistration">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Peserta *</label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center"><input type="radio" wire:model.live="jenis_peserta" value="pegawai_dinas" class="mr-2"><span class="text-sm">Pegawai Dinas</span></label>
                                        <label class="flex items-center"><input type="radio" wire:model.live="jenis_peserta" value="eksternal" class="mr-2"><span class="text-sm">Eksternal</span></label>
                                    </div>
                                    @error('jenis_peserta') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Peserta *</label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center"><input type="radio" wire:model.live="tipe_peserta" value="narasumber" class="mr-2"><span class="text-sm">Narasumber</span></label>
                                        <label class="flex items-center"><input type="radio" wire:model.live="tipe_peserta" value="peserta" class="mr-2"><span class="text-sm">Peserta</span></label>
                                    </div>
                                    @error('tipe_peserta') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                                    <input wire:model.live="name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                @if($jenis_peserta === 'pegawai_dinas')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">NIP/NIK *</label>
                                        <input wire:model.live="nip" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                        @error('nip') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                @else
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">NIP/NIK *</label>
                                        <input wire:model.live="nik" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                        @error('nik') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanda Tangan *</label>
                                    <div class="border border-gray-300 rounded-md p-2">
                                        <div wire:ignore
                                             x-data="{
                                                 init() {
                                                     this.$nextTick(() => {
                                                         const canvas = this.$refs.canvas;
                                                         if (!canvas || canvas._sigPad) return;
                                                         const sp = new SignaturePad(canvas, { backgroundColor: 'rgba(255,255,255,0)', penColor: 'rgb(0,0,0)' });
                                                         canvas._sigPad = sp;
                                                         sp.addEventListener('endStroke', () => $wire.set('signature_data', sp.toDataURL()));
                                                     });
                                                 },
                                                 clear() { const pad = this.$refs.canvas?._sigPad; if (pad) { pad.clear(); $wire.set('signature_data', ''); } }
                                             }">
                                            <canvas x-ref="canvas" width="400" height="150" class="w-full border rounded bg-white"></canvas>
                                            <div class="flex justify-end mt-2">
                                                <button type="button" @click="clear()" class="text-sm text-red-600 hover:text-red-800">Hapus</button>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" wire:model="signature_data">
                                    @error('signature_data') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="flex items-start gap-2">
                                        <input type="checkbox" wire:model.live="declaration" class="mt-1">
                                        <span class="text-sm text-gray-600">Saya menyatakan ini adalah benar tanda tangan saya</span>
                                    </label>
                                    @error('declaration') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="flex justify-end gap-2 mt-6">
                                <button type="button" wire:click="closeModal" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 text-sm">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Kirim Pendaftaran</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>

    @endif
</div>
