<?php

use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Meeting;
use App\Models\Participant;
use App\DTOs\Participant\RegisterParticipantDTO;

new class extends Component {
    public $meetingId;
    public $meeting;

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

    public $success = false;

    public function mount($meetingId)
    {
        $this->meetingId = $meetingId;
        $this->meeting = Meeting::with('meetingDays')->findOrFail($meetingId);
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

    public function submit()
    {
        $this->validate();

        $exists = Participant::where('meeting_id', $this->meetingId)
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
            'meeting_id' => $this->meetingId,
            'name' => $this->name,
            'jenis_peserta' => $this->jenis_peserta,
            'tipe_peserta' => $this->tipe_peserta,
            'nip' => $this->nip,
            'nik' => $this->nik,
            'signature_data' => $this->signature_data,
            'declaration' => $this->declaration,
            'registered_at' => now(),
        ]);

        $this->success = true;
    }

    public function closeModal()
    {
        $this->emitUp('closeRegistration');
    }
};
?>

<div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50" wire:click.self="closeModal">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
        @if($success)
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Pendaftaran Berhasil!</h3>
                <p class="text-gray-600 mb-4">Anda telah terdaftar sebagai {{ $tipe_peserta === 'narasumber' ? 'Narasumber' : 'Peserta' }}.</p>
                <p class="text-sm text-gray-500 mb-6">{{ $meeting->title }}</p>
                <button wire:click="closeModal" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Tutup</button>
            </div>
        @else
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Daftar Rapat</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="bg-blue-50 rounded-md p-3 mb-4">
                    <p class="text-sm text-blue-800 font-medium">{{ $meeting->title }}</p>
                    <p class="text-xs text-blue-600">{{ $meeting->location }} | {{ $meeting->start_time }} - {{ $meeting->end_time }}</p>
                </div>

                <form wire:submit="submit">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Peserta *</label>
                            <div class="flex gap-4">
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="jenis_peserta" value="pegawai_dinas" class="mr-2">
                                    <span class="text-sm">Pegawai Dinas</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="jenis_peserta" value="eksternal" class="mr-2">
                                    <span class="text-sm">Eksternal</span>
                                </label>
                            </div>
                            @error('jenis_peserta') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Peserta *</label>
                            <div class="flex gap-4">
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="tipe_peserta" value="narasumber" class="mr-2">
                                    <span class="text-sm">Narasumber</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="tipe_peserta" value="peserta" class="mr-2">
                                    <span class="text-sm">Peserta</span>
                                </label>
                            </div>
                            @error('tipe_peserta') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                            <input wire:model.live="name" type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        @if($jenis_peserta === 'pegawai_dinas')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIP *</label>
                                <input wire:model.live="nip" type="text" maxlength="20"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                @error('nip') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIK *</label>
                                <input wire:model.live="nik" type="text" maxlength="16"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                @error('nik') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanda Tangan *</label>
                            <div class="border border-gray-300 rounded-md p-2">
                                <canvas id="signature-pad" width="400" height="150" class="w-full border rounded bg-white"></canvas>
                                <div class="flex justify-end mt-2">
                                    <button type="button" onclick="clearSignature()" class="text-sm text-red-600 hover:text-red-800">Hapus</button>
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
                        <button type="button" wire:click="closeModal" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Kirim Pendaftaran</button>
                    </div>
                </form>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script>
        let signaturePad;

        document.addEventListener('livewire:initialized', () => {
            initSignaturePad();
        });

        Livewire.on('closeRegistration', () => {
            signaturePad = null;
        });

        function initSignaturePad() {
            const canvas = document.getElementById('signature-pad');
            if (canvas) {
                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgba(255, 255, 255, 0)',
                    penColor: 'rgb(0, 0, 0)'
                });

                const resizeCanvas = () => {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext('2d').scale(ratio, ratio);
                };
                resizeCanvas();
                window.addEventListener('resize', resizeCanvas);

                signaturePad.addEventListener('endStroke', () => {
                    @this.set('signature_data', signaturePad.toDataURL());
                });
            }
        }

        function clearSignature() {
            if (signaturePad) {
                signaturePad.clear();
                @this.set('signature_data', '');
            }
        }

        document.addEventListener('click', (e) => {
            if (e.target.closest('[wire\\:click="closeModal"]')) {
                setTimeout(() => {
                    const canvas = document.getElementById('signature-pad');
                    if (canvas && !signaturePad) {
                        initSignaturePad();
                    }
                }, 100);
            }
        });
    </script>
</div>
