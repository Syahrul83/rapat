<?php

use Livewire\Component;
use App\Models\Notulensi;

new class extends Component {
    public $search = '';

    public function notulensis()
    {
        return Notulensi::with(['meeting', 'notulen', 'kepala'])
            ->when($this->search, fn($q) => $q->whereHas('meeting', fn($m) => $m->where('title', 'like', "%{$this->search}%")))
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    public function delete($id)
    {
        Notulensi::findOrFail($id)->delete();
        session()->flash('success', 'Notulensi berhasil dihapus');
    }
};
?>

<div>
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-body-sm">{{ session('success') }}</div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-headline-md text-primary">Notulensi</h1>
        <a href="{{ route('admin.notulensis.create') }}" class="bg-primary text-on-primary px-4 py-2 rounded-lg text-label-md font-bold hover:brightness-95 transition-all">+ Tambah Notulensi</a>
    </div>

    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari notulensi..."
            class="w-full md:w-96 px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
    </div>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-surface-container-low text-primary text-label-md border-b border-outline-variant">
                <tr>
                    <th class="px-4 py-4">NO</th>
                    <th class="px-4 py-4">JUDUL RAPAT</th>
                    <th class="px-4 py-4">NOTULEN</th>
                    <th class="px-4 py-4">KEPALA</th>
                    <th class="px-4 py-4">TANGGAL</th>
                    <th class="px-4 py-4 text-right">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @php $no = 1; @endphp
                @forelse($this->notulensis() as $notulensi)
                    <tr class="hover:bg-surface-container-low transition-colors group">
                        <td class="px-4 py-4 text-body-md text-on-surface-variant">{{ $no++ }}</td>
                        <td class="px-4 py-4 text-body-md text-primary font-semibold">{{ $notulensi->meeting->title ?? '-' }}</td>
                        <td class="px-4 py-4 text-body-md text-secondary">{{ $notulensi->notulen->name ?? '-' }}</td>
                        <td class="px-4 py-4 text-body-md text-secondary">{{ $notulensi->kepala->name ?? '-' }}</td>
                        <td class="px-4 py-4 text-body-md text-secondary">{{ $notulensi->notulensi_date->format('d M Y') }}</td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.notulensis.edit', $notulensi) }}" class="text-label-sm text-secondary hover:underline">Edit</a>
                                <a href="{{ route('admin.notulensis.pdf', $notulensi) }}" target="_blank" class="text-label-sm text-primary hover:underline">Cetak PDF</a>
                                <button wire:click="delete({{ $notulensi->id }})" wire:confirm="Hapus notulensi ini?" class="text-label-sm text-error hover:underline">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-secondary text-body-md">Belum ada data notulensi</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $this->notulensis()->links() }}</div>
</div>
