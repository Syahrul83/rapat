<?php

use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Meeting;
use App\Models\Participant;

new class extends Component {
    public $search = '';
    public $showModal = false;
    public $selectedMeetingId = null;

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

    <style>
        .hero-grid {
            background-image: linear-gradient(rgba(0, 61, 91, 0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 61, 91, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
        }
    </style>

    <header class="bg-white border-b border-gray-100 py-4 px-6 md:px-12 flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <h1 class="text-2xl font-bold text-brand-navy">Meeting Digital Service</h1>
        </div>
        <nav>
            <a href="{{ route('login') }}" class="bg-brand-navy text-white px-5 py-2 rounded-md flex items-center text-sm font-medium hover:bg-slate-800 transition-colors">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
                LOGIN ADMIN
            </a>
        </nav>
    </header>

    <section class="bg-brand-blue-light py-16 px-6 md:px-12 text-center relative overflow-hidden hero-grid">
        <div class="max-w-4xl mx-auto relative z-10">
            <span class="text-brand-blue-dark font-bold text-xs uppercase tracking-widest mb-2 block">SISTEM KEHADIRAN DIGITAL</span>
            <h2 class="text-4xl md:text-5xl font-extrabold text-brand-blue-dark mb-6">Kehadiran Rapat</h2>
            <p class="text-brand-blue-dark opacity-80 max-w-2xl mx-auto mb-10 text-lg leading-relaxed">
                Kelola dan pantau kehadiran seluruh agenda kedinasan dalam satu platform terintegrasi yang transparan dan akuntabel.
            </p>
            <div class="max-w-2xl mx-auto bg-white p-2 rounded-lg shadow-lg flex flex-col md:flex-row items-center gap-2">
                <div class="relative flex-grow w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari berdasarkan nama kegiatan atau kode..."
                        class="block w-full pl-10 pr-3 py-3 border-none focus:ring-0 text-sm">
                </div>
                <button class="bg-brand-blue-light text-brand-blue-dark px-8 py-3 rounded-md font-semibold text-sm w-full md:w-auto hover:bg-gray-100 transition-all">
                    CARI KEGIATAN
                </button>
            </div>
        </div>
    </section>

    <main class="max-w-7xl mx-auto py-12 px-6 md:px-12">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
            <div>
                <h3 class="text-3xl font-bold text-brand-navy mb-2">Agenda Aktif</h3>
                <p class="text-brand-text-muted">Menampilkan daftar kegiatan yang sedang berlangsung atau akan datang.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @forelse($this->meetings() as $meeting)
                @php
                    $today = now()->toDateString();
                    $currentTime = now()->format('H:i');
                    $startTime = substr($meeting->start_time, 0, 5);
                    $endTime = substr($meeting->end_time, 0, 5);
                    $lastDay = $meeting->meetingDays->sortByDesc('date')->first();
                    $lastDate = $lastDay ? $lastDay->date->toDateString() : null;
                    $isAfterLastDay = $lastDate && $lastDate < $today;
                    $isLastDayToday = $lastDate === $today;
                    $meetingToday = $meeting->meetingDays->first(fn($d) => $d->date->toDateString() === $today);
                    $isInSession = $meetingToday && $currentTime >= $startTime && $currentTime <= $endTime;
                    if ($isAfterLastDay || ($isLastDayToday && $currentTime > $endTime)) {
                        $btnState = 'expired';
                        $btnLabel = 'Pendaftaran Ditutup';
                    } elseif ($isInSession) {
                        $btnState = 'open';
                        $btnLabel = 'Daftar Hadir';
                    } else {
                        $btnState = 'closed';
                        $btnLabel = 'Belum Dibuka';
                    }
                @endphp
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col hover:shadow-md transition-shadow {{ $btnState === 'expired' ? 'opacity-50' : '' }}">
                    <div class="flex justify-between items-start mb-6">
                        <div class="bg-gray-100 text-brand-navy w-10 h-10 flex items-center justify-center font-bold text-lg rounded">{{ sprintf("%02d", $loop->index + 1) }}</div>
                        @if($btnState === 'open')
                            <span class="bg-brand-blue-light text-brand-blue-dark px-3 py-1 rounded text-xs font-bold tracking-wider">BERLANGSUNG</span>
                        @elseif($btnState === 'closed')
                            <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded text-xs font-bold tracking-wider">MENDATANG</span>
                        @endif
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 mb-4">{{ $meeting->title }}</h4>
                    <div class="space-y-3 mb-8 text-sm text-brand-text-muted">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                            {{ $this->getDates($meeting) }}, {{ $meeting->start_time }} - {{ $meeting->end_time }}
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                            {{ $meeting->location }}
                        </div>
                        @if($meeting->description)
                            <div class="flex items-start">
                                <svg class="w-4 h-4 mr-3 text-gray-400 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                                <p>{{ Str::limit($meeting->description, 80) }}</p>
                            </div>
                        @endif
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                            {{ $meeting->participants_count }} orang
                        </div>
                    </div>
                    @if($btnState === 'open')
                        <button wire:click="openModal({{ $meeting->id }})" class="mt-auto w-full bg-brand-blue-light text-brand-blue-dark py-3 rounded-lg flex items-center justify-center font-bold text-sm hover:bg-gray-100 transition-colors">
                            {{ $btnLabel }} <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                        </button>
                    @else
                        <button disabled class="mt-auto w-full bg-gray-100 text-gray-400 py-3 rounded-lg flex items-center justify-center font-bold text-sm cursor-not-allowed">
                            {{ $btnLabel }}
                        </button>
                    @endif
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-brand-text-muted">Belum ada jadwal rapat saat ini</div>
            @endforelse
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 py-6 px-6 md:px-12 mt-auto">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center text-sm">
            <div class="mb-4 md:mb-0">
                <h5 class="font-bold text-brand-navy">MEETING DIGITAL SERVICE</h5>
            </div>
            <div class="text-brand-text-muted mb-4 md:mb-0 text-center">
                &copy; {{ date('Y') }} Meeting Digital Service. Seluruh Hak Cipta Dilindungi.
            </div>
            <div class="flex space-x-6">
                <a class="text-brand-text-muted hover:text-brand-blue-dark" href="#">Kebijakan Privasi</a>
                <a class="text-brand-text-muted hover:text-brand-blue-dark" href="#">Syarat &amp; Ketentuan</a>
                <a class="text-brand-text-muted hover:text-brand-blue-dark" href="#">Kontak Kami</a>
            </div>
        </div>
    </footer>

    @if($showModal && $selectedMeetingId)
        @php $modalMeeting = $this->getSelectedMeeting(); @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-brand-navy/40 backdrop-blur-sm" wire:click="closeModal">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-lg overflow-hidden flex flex-col" onclick="event.stopPropagation()">
                @if($registrationSuccess)
                    <div class="p-6 text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-brand-navy mb-2">Pendaftaran Berhasil!</h3>
                        <p class="text-brand-text-muted mb-4">Anda terdaftar sebagai {{ $tipe_peserta === 'narasumber' ? 'Narasumber' : 'Peserta' }}.</p>
                        <button wire:click="closeModal" class="px-6 py-2 bg-brand-navy text-white rounded-md hover:bg-slate-800">Tutup</button>
                    </div>
                @else
                    <div class="flex justify-between items-center p-6 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-brand-navy">Daftar Rapat</h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-6 overflow-y-auto max-h-[80vh]">
                        @if($modalMeeting)
                            <div class="bg-brand-blue-light p-4 rounded-lg">
                                <h4 class="text-brand-blue-dark font-bold">{{ $modalMeeting->title }}</h4>
                                <p class="text-brand-blue-dark text-sm opacity-80">{{ $modalMeeting->location }} | {{ $modalMeeting->start_time }} - {{ $modalMeeting->end_time }}</p>
                            </div>
                        @endif
                        <form wire:submit="submitRegistration" class="space-y-5">
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700">Jenis Peserta *</label>
                                <div class="flex items-center space-x-6">
                                    <label class="flex items-center text-sm text-gray-600 cursor-pointer">
                                        <input type="radio" wire:model.live="jenis_peserta" value="pegawai_dinas" class="w-4 h-4 text-brand-navy focus:ring-brand-navy mr-2"> Pegawai Dinas
                                    </label>
                                    <label class="flex items-center text-sm text-gray-600 cursor-pointer">
                                        <input type="radio" wire:model.live="jenis_peserta" value="eksternal" class="w-4 h-4 text-brand-navy focus:ring-brand-navy mr-2"> Eksternal
                                    </label>
                                </div>
                                @error('jenis_peserta') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700">Tipe Peserta *</label>
                                <div class="flex items-center space-x-6">
                                    <label class="flex items-center text-sm text-gray-600 cursor-pointer">
                                        <input type="radio" wire:model.live="tipe_peserta" value="narasumber" class="w-4 h-4 text-brand-navy focus:ring-brand-navy mr-2"> Narasumber
                                    </label>
                                    <label class="flex items-center text-sm text-gray-600 cursor-pointer">
                                        <input type="radio" wire:model.live="tipe_peserta" value="peserta" class="w-4 h-4 text-brand-navy focus:ring-brand-navy mr-2"> Peserta
                                    </label>
                                </div>
                                @error('tipe_peserta') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-bold text-gray-700">Nama Lengkap *</label>
                                <input wire:model.live="name" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-brand-blue-light focus:border-brand-navy outline-none transition-all">
                                @error('name') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            @if($jenis_peserta === 'pegawai_dinas')
                                <div class="space-y-1">
                                    <label class="block text-sm font-bold text-gray-700">NIP/NIK *</label>
                                    <input wire:model.live="nip" type="number" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-brand-blue-light focus:border-brand-navy outline-none transition-all">
                                    @error('nip') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                </div>
                            @else
                                <div class="space-y-1">
                                    <label class="block text-sm font-bold text-gray-700">NIP/NIK *</label>
                                    <input wire:model.live="nik" type="number" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-brand-blue-light focus:border-brand-navy outline-none transition-all">
                                    @error('nik') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                </div>
                            @endif
                            <div class="space-y-1">
                                <label class="block text-sm font-bold text-gray-700">Tanda Tangan *</label>
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
                                            <button type="button" @click="clear()" class="text-red-500 text-xs font-medium hover:underline">Hapus</button>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" wire:model="signature_data">
                                @error('signature_data') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                            <label class="flex items-start text-sm text-gray-600 cursor-pointer">
                                <input type="checkbox" wire:model.live="declaration" class="mt-1 w-4 h-4 text-brand-navy rounded border-gray-300 focus:ring-brand-navy mr-3">
                                <span>Saya menyatakan ini adalah benar tanda tangan saya</span>
                            </label>
                            @error('declaration') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            <div class="flex justify-end space-x-3 pt-2">
                                <button type="button" wire:click="closeModal" class="px-6 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">Batal</button>
                                <button type="submit" class="px-6 py-2 bg-brand-navy text-white rounded-md text-sm font-bold hover:bg-slate-800 transition-colors">Kirim Pendaftaran</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
