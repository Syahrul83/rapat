<?php

use Livewire\Component;
use App\Models\UploadedFile;

new class extends Component {
    public $search = '';

    public function files()
    {
        return UploadedFile::query()
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    public function delete($id)
    {
        $file = UploadedFile::findOrFail($id);

        if ($file->file_path && \Storage::disk('public')->exists($file->file_path)) {
            \Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();
        session()->flash('success', 'File berhasil dihapus');
    }
};
?>

<div>
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-body-sm">{{ session('success') }}</div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-headline-md text-primary">Upload File</h1>
        <a href="{{ route('admin.uploaded-files.create') }}" class="bg-primary text-on-primary px-4 py-2 rounded-lg text-label-md font-bold hover:brightness-95 transition-all">+ Tambah File</a>
    </div>

    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari file..."
            class="w-full md:w-96 px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
    </div>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-surface-container-low text-primary text-label-md border-b border-outline-variant">
                <tr>
                    <th class="px-4 py-4">No</th>
                    <th class="px-4 py-4">Judul</th>
                    <th class="px-4 py-4">Tanggal</th>
                    <th class="px-4 py-4">Jenis</th>
                    <th class="px-4 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @php $no = 1; @endphp
                @forelse($this->files() as $file)
                    <tr class="hover:bg-surface-container-low transition-colors group">
                        <td class="px-4 py-4 text-body-md text-on-surface-variant">{{ $no++ }}</td>
                        <td class="px-4 py-4 text-body-md text-primary font-semibold">{{ $file->title }}</td>
                        <td class="px-4 py-4 text-body-md text-secondary">{{ $file->upload_date->format('d M Y') }}</td>
                        <td class="px-4 py-4 text-body-md text-secondary">
                            @if($file->isPdf())
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded-full text-label-sm font-bold">
                                    <span class="material-symbols-outlined text-[16px]">picture_as_pdf</span> PDF
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-label-sm font-bold">
                                    <span class="material-symbols-outlined text-[16px]">image</span> Gambar
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.uploaded-files.view', $file) }}" target="_blank" class="text-label-sm text-primary hover:underline">View File</a>
                                <a href="{{ route('admin.uploaded-files.edit', $file) }}" class="text-label-sm text-secondary hover:underline">Edit</a>
                                <button wire:click="delete({{ $file->id }})" wire:confirm="Hapus file ini?" class="text-label-sm text-error hover:underline">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-secondary text-body-md">Belum ada file</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $this->files()->links() }}</div>
</div>
