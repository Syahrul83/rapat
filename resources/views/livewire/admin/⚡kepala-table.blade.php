<?php

use Livewire\Component;
use App\Models\Kepala;

new class extends Component {
    public $search = '';

    public function kepalas()
    {
        return Kepala::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('nip', 'like', "%{$this->search}%"))
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    public function toggle($id)
    {
        $kepala = Kepala::findOrFail($id);
        $kepala->update(['is_active' => !$kepala->is_active]);
        session()->flash('success', $kepala->is_active ? 'Kepala diaktifkan' : 'Kepala dinonaktifkan');
    }
};
?>

<div>
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-body-sm">{{ session('success') }}</div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-headline-md text-primary">Kepala</h1>
    </div>

    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari kepala..."
            class="w-full md:w-96 px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
    </div>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-surface-container-low text-primary text-label-md border-b border-outline-variant">
                <tr>
                    <th class="px-4 py-4">NO</th>
                    <th class="px-4 py-4">NAMA</th>
                    <th class="px-4 py-4">NIP</th>
                    <th class="px-4 py-4">TTD</th>
                    <th class="px-4 py-4">STATUS</th>
                    <th class="px-4 py-4 text-right">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @php $no = 1; @endphp
                @forelse($this->kepalas() as $kepala)
                    <tr class="hover:bg-surface-container-low transition-colors group">
                        <td class="px-4 py-4 text-body-md text-on-surface-variant">{{ $no++ }}</td>
                        <td class="px-4 py-4 text-body-md text-primary font-semibold">{{ $kepala->name }}</td>
                        <td class="px-4 py-4 text-body-md text-secondary">{{ $kepala->nip }}</td>
                        <td class="px-4 py-4">
                            @if($kepala->ttd_image)
                                <img src="{{ asset('storage/' . $kepala->ttd_image) }}" alt="TTD" class="h-10">
                            @else
                                <span class="text-label-sm text-on-surface-variant">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <span class="px-2 py-1 rounded-full text-label-sm font-bold {{ $kepala->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $kepala->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.kepalas.edit', $kepala) }}" class="text-label-sm text-secondary hover:underline">Edit</a>
                                <button wire:click="toggle({{ $kepala->id }})" class="text-label-sm {{ $kepala->is_active ? 'text-orange-600' : 'text-green-600' }} hover:underline">
                                    {{ $kepala->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-secondary text-body-md">Belum ada data kepala</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $this->kepalas()->links() }}</div>
</div>
