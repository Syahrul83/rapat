<?php

use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Notulen;

new class extends Component {
    use WithFileUploads;

    public $notulenId;

    #[Validate('required|max:255')]
    public $name = '';

    #[Validate('required|max:255')]
    public $nip = '';

    #[Validate('nullable|image|max:2048')]
    public $ttd_image = null;

    public $existingTtd = null;

    public function mount($id)
    {
        $notulen = Notulen::findOrFail($id);
        $this->notulenId = $notulen->id;
        $this->name = $notulen->name;
        $this->nip = $notulen->nip;
        $this->existingTtd = $notulen->ttd_image;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'nip' => $this->nip,
        ];

        if ($this->ttd_image) {
            $data['ttd_image'] = $this->ttd_image->store('ttd', 'public');
        }

        Notulen::findOrFail($this->notulenId)->update($data);

        session()->flash('success', 'Notulen berhasil diperbarui');
        return redirect()->route('admin.notulens');
    }
};
?>

<div>
    <div class="max-w-lg">
        <a href="{{ route('admin.notulens') }}" class="text-label-md text-primary hover:underline mb-4 inline-flex items-center gap-1">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
        </a>
        <h1 class="text-headline-md text-primary mb-6">Edit Notulen</h1>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
            <form wire:submit="save" class="space-y-5">
                <div>
                    <label class="block text-label-md text-on-surface mb-1">Nama *</label>
                    <input wire:model.live="name" type="text"
                        class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
                    @error('name') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-label-md text-on-surface mb-1">NIP *</label>
                    <input wire:model.live="nip" type="text"
                        class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
                    @error('nip') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-label-md text-on-surface mb-1">Upload TTD</label>
                    <input wire:model.live="ttd_image" type="file" accept="image/*"
                        class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-primary file:text-on-primary file:text-label-sm">
                    @error('ttd_image') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                    @if($ttd_image)
                        <img src="{{ $ttd_image->temporaryUrl() }}" alt="Preview TTD" class="mt-2 h-16">
                    @elseif($existingTtd)
                        <img src="{{ asset('storage/' . $existingTtd) }}" alt="Current TTD" class="mt-2 h-16">
                        <p class="text-label-sm text-on-surface-variant mt-1">TTD saat ini. Upload untuk mengganti.</p>
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('admin.notulens') }}" class="px-6 py-2 border border-outline-variant rounded-lg text-label-md font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-primary text-on-primary rounded-lg text-label-md font-bold hover:brightness-95 transition-all">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
