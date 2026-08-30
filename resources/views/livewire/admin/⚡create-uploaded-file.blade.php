<?php

use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\UploadedFile;

new class extends Component {
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public $title = '';

    #[Validate('required|date')]
    public $upload_date = '';

    #[Validate('required|file|mimes:jpg,jpeg,png,webp,pdf|max:10240')]
    public $file = null;

    public function save()
    {
        $this->validate();

        $storedPath = $this->file->store('uploaded-files', 'public');

        UploadedFile::create([
            'title' => $this->title,
            'upload_date' => $this->upload_date,
            'file_path' => $storedPath,
            'file_name' => $this->file->getClientOriginalName(),
            'mime_type' => $this->file->getMimeType(),
            'file_size' => $this->file->getSize(),
        ]);

        session()->flash('success', 'File berhasil diupload');
        return redirect()->route('admin.uploaded-files');
    }
};
?>

<div>
    <div class="max-w-2xl">
        <a href="{{ route('admin.uploaded-files') }}" class="text-label-md text-primary hover:underline mb-4 inline-flex items-center gap-1">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
        </a>
        <h1 class="text-headline-md text-primary mb-6">Tambah File</h1>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
            <form wire:submit="save" class="space-y-5">
                <div>
                    <label class="block text-label-md text-on-surface mb-1">Judul <span class="text-error">*</span></label>
                    <input wire:model.live="title" type="text" required
                        class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
                    @error('title') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-label-md text-on-surface mb-1">Tanggal <span class="text-error">*</span></label>
                    <input wire:model.live="upload_date" type="date" required
                        class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
                    @error('upload_date') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-label-md text-on-surface mb-1">File (Gambar / PDF) <span class="text-error">*</span></label>
                    <input wire:model.live="file" type="file" required accept="image/*,application/pdf"
                        class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-primary file:text-on-primary file:text-label-sm">
                    @error('file') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                    @if($file)
                        <div class="mt-2">
                            @php
                                $mime = $file->getMimeType();
                            @endphp
                            @if(str_starts_with($mime, 'image/'))
                                <img src="{{ $file->temporaryUrl() }}" alt="Preview" class="h-32 object-contain rounded-lg border border-outline-variant">
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded-full text-label-sm font-bold">
                                    <span class="material-symbols-outlined text-[16px]">picture_as_pdf</span> {{ $file->getClientOriginalName() }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('admin.uploaded-files') }}" class="px-6 py-2 border border-outline-variant rounded-lg text-label-md font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-primary text-on-primary rounded-lg text-label-md font-bold hover:brightness-95 transition-all">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
