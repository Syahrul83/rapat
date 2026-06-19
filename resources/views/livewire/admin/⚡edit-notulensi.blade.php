<?php

use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Meeting;
use App\Models\Notulen;
use App\Models\Kepala;
use App\Models\Notulensi;

new class extends Component {
    use WithFileUploads;

    public $notulensiId;

    #[Validate('required|exists:meetings,id')]
    public $meeting_id = '';

    #[Validate('required|exists:notulens,id')]
    public $notulen_id = '';

    #[Validate('required|exists:kepalas,id')]
    public $kepala_id = '';

    public $meeting_title = '';
    public $meeting_date = '';
    public $location = '';

    #[Validate('required')]
    public $isi_notulensi = '';

    public $photos = [];
    public $photos_upload = [];
    public $deletePhotoIds = [];

    public function getMeetingsProperty()
    {
        return Meeting::where('status', 'published')->orWhere('id', $this->meeting_id)->orderByDesc('created_at')->get();
    }

    public function getNotulensProperty()
    {
        return Notulen::where('is_active', true)->get();
    }

    public function getKepalasProperty()
    {
        return Kepala::where('is_active', true)->get();
    }

    public function removePhoto($photoId)
    {
        $this->deletePhotoIds[] = $photoId;
    }

    public function mount($id)
    {
        $notulensi = Notulensi::with('photos')->findOrFail($id);
        $this->notulensiId = $notulensi->id;
        $this->meeting_id = $notulensi->meeting_id;
        $this->notulen_id = $notulensi->notulen_id;
        $this->kepala_id = $notulensi->kepala_id;
        $this->meeting_date = $notulensi->meeting_date->format('Y-m-d');
        $this->location = $notulensi->location;
        $this->isi_notulensi = $notulensi->isi_notulensi;
        $this->photos = $notulensi->photos;
    }

    public function save()
    {
        $this->validate([
            'meeting_id' => 'required|exists:meetings,id',
            'notulen_id' => 'required|exists:notulens,id',
            'kepala_id' => 'required|exists:kepalas,id',
            'meeting_date' => 'required|date',
            'location' => 'required',
            'isi_notulensi' => 'required',
            'photos_upload.*' => 'nullable|image|max:5120',
        ]);

        $notulensi = Notulensi::findOrFail($this->notulensiId);

        $notulensi->update([
            'meeting_id' => $this->meeting_id,
            'notulen_id' => $this->notulen_id,
            'kepala_id' => $this->kepala_id,
            'notulensi_date' => now(),
            'meeting_date' => $this->meeting_date,
            'location' => $this->location,
            'isi_notulensi' => $this->isi_notulensi,
        ]);

        if ($this->deletePhotoIds) {
            $notulensi->photos()->whereIn('id', $this->deletePhotoIds)->delete();
        }

        if ($this->photos_upload) {
            foreach ($this->photos_upload as $photo) {
                $notulensi->photos()->create([
                    'photo_path' => $photo->store('notulensi-photos', 'public'),
                ]);
            }
        }

        session()->flash('success', 'Notulensi berhasil diperbarui');
        return redirect()->route('admin.notulensis');
    }
};
?>

<div>
    <div class="max-w-2xl">
        <a href="{{ route('admin.notulensis') }}" class="text-label-md text-primary hover:underline mb-4 inline-flex items-center gap-1">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
        </a>
        <h1 class="text-headline-md text-primary mb-6">Edit Notulensi</h1>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
            <form wire:submit="save" class="space-y-5">
                <div>
                    <label class="block text-label-md text-on-surface mb-1">Judul Rapat *</label>
                    <select wire:model.live="meeting_id"
                        class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
                        @foreach($this->meetings as $meeting)
                            <option value="{{ $meeting->id }}">{{ $meeting->title }}</option>
                        @endforeach
                    </select>
                    @error('meeting_id') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-label-md text-on-surface mb-1">Tanggal Kegiatan *</label>
                        <input wire:model.live="meeting_date" type="date"
                            class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
                        @error('meeting_date') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-label-md text-on-surface mb-1">Lokasi *</label>
                        <input wire:model.live="location" type="text"
                            class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
                        @error('location') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-label-md text-on-surface mb-1">Notulen *</label>
                        <select wire:model.live="notulen_id"
                            class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
                            @foreach($this->notulens as $notulen)
                                <option value="{{ $notulen->id }}">{{ $notulen->name }}</option>
                            @endforeach
                        </select>
                        @error('notulen_id') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-label-md text-on-surface mb-1">Kepala *</label>
                        <select wire:model.live="kepala_id"
                            class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
                            @foreach($this->kepalas as $kepala)
                                <option value="{{ $kepala->id }}">{{ $kepala->name }}</option>
                            @endforeach
                        </select>
                        @error('kepala_id') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-label-md text-on-surface mb-1">Isi Notulensi *</label>
                    <textarea wire:model.live="isi_notulensi" rows="8"
                        class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md"></textarea>
                    @error('isi_notulensi') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-label-md text-on-surface mb-1">Foto Saat Ini</label>
                    @if($photos)
                        <div class="flex gap-2 flex-wrap">
                            @foreach($photos as $photo)
                                <div class="relative">
                                    <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto" class="h-20 w-20 object-cover rounded-lg">
                                    @if(in_array($photo->id, $deletePhotoIds))
                                        <span class="absolute inset-0 bg-black/50 flex items-center justify-center text-label-sm text-white rounded-lg">Dihapus</span>
                                    @else
                                        <button type="button" wire:click="removePhoto({{ $photo->id }})" class="absolute -top-2 -right-2 w-5 h-5 bg-error text-white rounded-full text-label-xs flex items-center justify-center">&times;</button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-label-sm text-on-surface-variant">Belum ada foto</p>
                    @endif
                </div>

                <div>
                    <label class="block text-label-md text-on-surface mb-1">Tambah Foto Baru</label>
                    <input wire:model.live="photos_upload" type="file" multiple accept="image/*"
                        class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-primary file:text-on-primary file:text-label-sm">
                    @error('photos_upload.*') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                    @if($photos_upload)
                        <div class="flex gap-2 mt-2 flex-wrap">
                            @foreach($photos_upload as $photo)
                                <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="h-20 w-20 object-cover rounded-lg">
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('admin.notulensis') }}" class="px-6 py-2 border border-outline-variant rounded-lg text-label-md font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-primary text-on-primary rounded-lg text-label-md font-bold hover:brightness-95 transition-all">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
