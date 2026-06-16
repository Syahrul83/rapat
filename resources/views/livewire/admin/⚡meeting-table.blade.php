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

    public function confirmDelete($id)
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
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-6">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">Jadwal Rapat</a>
                    <a href="{{ route('admin.meetings') }}" class="text-sm text-blue-600 font-semibold">Rapat</a>
                    <a href="{{ route('admin.reports') }}" class="text-sm text-gray-600 hover:text-gray-900">Laporan</a>
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('admin.users') }}" class="text-sm text-gray-600 hover:text-gray-900">Users</a>
                    @endif
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Daftar Jadwal Rapat</h1>
            <a href="{{ route('admin.meetings.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                + Tambah Jadwal
            </a>
        </div>

        <div class="mb-4">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari rapat..."
                class="w-full md:w-96 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peserta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $no = 1; @endphp
                    @forelse($this->meetings() as $meeting)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $no++ }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $meeting->title }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $this->getDates($meeting) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $meeting->location }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($meeting->status->value === 'published')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $meeting->participants_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <a href="{{ route('admin.meetings.show', $meeting) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                <a href="{{ route('admin.meetings.edit', $meeting) }}" class="text-yellow-600 hover:text-yellow-800">Edit</a>
                                @if($meeting->status->value === 'draft')
                                    <button wire:click="publish({{ $meeting->id }})" class="text-green-600 hover:text-green-800">Publish</button>
                                @else
                                    <button wire:click="unpublish({{ $meeting->id }})" class="text-orange-600 hover:text-orange-800">Unpublish</button>
                                @endif
                                <button wire:click="confirmDelete({{ $meeting->id }})" class="text-red-600 hover:text-red-800">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada jadwal rapat</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $this->meetings()->links() }}
        </div>

        @if($confirmDelete)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                    <h3 class="text-lg font-bold mb-4">Konfirmasi Hapus</h3>
                    <p class="mb-4">Apakah Anda yakin ingin menghapus rapat ini?</p>
                    <div class="flex justify-end space-x-2">
                        <button wire:click="$set('confirmDelete', null)" class="px-4 py-2 border rounded-md">Batal</button>
                        <button wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Hapus</button>
                    </div>
                </div>
            </div>
        @endif
    </main>
</div>
