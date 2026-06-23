<?php

use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Meeting;
use App\Actions\Meeting\DeleteMeetingAction;
use App\Actions\Meeting\PublishMeetingAction;
use App\Actions\Meeting\UnpublishMeetingAction;

new class extends Component {
    public $search = '';
    public $confirmDelete = null;

    public function meetings()
    {
        return Meeting::query()
            ->withCount('participants')
            ->with('meetingDays')
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    public function publish($id)
    {
        app(PublishMeetingAction::class)->execute($id);
        session()->flash('success', 'Rapat berhasil dipublikasikan');
    }

    public function unpublish($id)
    {
        app(UnpublishMeetingAction::class)->execute($id);
        session()->flash('success', 'Publikasi rapat dibatalkan');
    }

    public function confirmDeleteMeeting($id)
    {
        $this->confirmDelete = $id;
    }

    public function delete()
    {
        app(DeleteMeetingAction::class)->execute($this->confirmDelete);
        $this->confirmDelete = null;
        session()->flash('success', 'Rapat berhasil dihapus');
    }

    public function getDates($meeting)
    {
        return $meeting->meetingDays->pluck('date')->map(fn($d) => $d->format('d M Y'))->implode(', ');
    }
};
?>

<div>
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-body-sm">{{ session('success') }}</div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-headline-md text-primary">Daftar Jadwal Rapat</h1>
        <a href="{{ route('admin.meetings.create') }}" class="bg-primary text-on-primary px-4 py-2 rounded-lg text-label-md font-bold hover:brightness-95 transition-all">+ Tambah Jadwal</a>
    </div>

    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari rapat..."
            class="w-full md:w-96 px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
    </div>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-surface-container-low text-primary text-label-md border-b border-outline-variant">
                <tr>
                    <th class="px-4 py-4">No</th>
                    <th class="px-4 py-4">Judul</th>
                    <th class="px-4 py-4">Tanggal</th>
                    <th class="px-4 py-4">Lokasi</th>
                    <th class="px-4 py-4">Status</th>
                    <th class="px-4 py-4">Peserta</th>
                    <th class="px-4 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @php $no = 1; @endphp
                @forelse($this->meetings() as $meeting)
                    <tr class="hover:bg-surface-container-low transition-colors group">
                        <td class="px-4 py-4 text-body-md text-on-surface-variant">{{ $no++ }}</td>
                        <td class="px-4 py-4 text-body-md text-primary font-semibold">{{ $meeting->title }}</td>
                        <td class="px-4 py-4 text-body-md text-secondary">{{ $this->getDates($meeting) }}</td>
                        <td class="px-4 py-4 text-body-md text-secondary">{{ $meeting->location }}</td>
                        <td class="px-4 py-4">
                            @if($meeting->status->value === 'published')
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-label-sm font-bold">Published</span>
                            @else
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-label-sm font-bold">Draft</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-body-md text-secondary">{{ $meeting->participants_count }}</td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.meetings.show', $meeting) }}" class="text-label-sm text-primary hover:underline">View</a>
                                <a href="{{ route('admin.meetings.edit', $meeting) }}" class="text-label-sm text-secondary hover:underline">Edit</a>
                                @if($meeting->status->value === 'draft')
                                    <button wire:click="publish({{ $meeting->id }})" class="text-label-sm text-green-600 hover:underline">Publish</button>
                                @else
                                    <button wire:click="unpublish({{ $meeting->id }})" class="text-label-sm text-orange-600 hover:underline">Unpublish</button>
                                @endif
                                <button wire:click="confirmDeleteMeeting({{ $meeting->id }})" class="text-label-sm text-error hover:underline">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-secondary text-body-md">Belum ada jadwal rapat</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $this->meetings()->links() }}</div>

    @if($confirmDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-brand-navy/40 backdrop-blur-sm" wire:click="$set('confirmDelete', null)">
            <div class="bg-surface-container-lowest rounded-xl shadow-lg max-w-md w-full mx-4 p-6" onclick="event.stopPropagation()">
                <h3 class="text-headline-md text-primary mb-2">Konfirmasi Hapus</h3>
                <p class="text-body-md text-secondary mb-6">Apakah Anda yakin ingin menghapus rapat ini?</p>
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('confirmDelete', null)" class="px-6 py-2 border border-outline-variant rounded-lg text-label-md font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors">Batal</button>
                    <button wire:click="delete" class="px-6 py-2 bg-error text-on-error rounded-lg text-label-md font-bold hover:brightness-95 transition-all">Hapus</button>
                </div>
            </div>
        </div>
    @endif
</div>
