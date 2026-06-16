<?php

use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\User;
use App\Enums\UserRole;

new class extends Component {
    public $search = '';
    public $confirmDelete = null;

    public function users()
    {
        return User::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    public function confirmDelete($id)
    {
        $this->confirmDelete = $id;
    }

    public function delete()
    {
        if ($this->confirmDelete === auth()->id()) {
            session()->flash('error', 'Tidak bisa menghapus akun sendiri');
            $this->confirmDelete = null;
            return;
        }
        User::findOrFail($this->confirmDelete)->delete();
        $this->confirmDelete = null;
        session()->flash('success', 'User berhasil dihapus');
    }
};
?>

<div>
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-body-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-body-sm">{{ session('error') }}</div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-headline-md text-primary">Manajemen User</h1>
        <a href="{{ route('admin.users.create') }}" class="bg-primary text-on-primary px-4 py-2 rounded-lg text-label-md font-bold hover:brightness-95 transition-all">+ Tambah User</a>
    </div>

    <div class="flex items-center gap-3 mb-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari user..."
            class="flex-1 md:w-96 px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
        <button class="px-4 py-2 border border-outline-variant rounded-lg text-label-md font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-[18px]">filter_alt</span> Filter
        </button>
        <button class="px-4 py-2 border border-outline-variant rounded-lg text-label-md font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-[18px]">download</span> Export
        </button>
    </div>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-surface-container-low text-primary text-label-md border-b border-outline-variant">
                <tr>
                    <th class="px-4 py-4">NO</th>
                    <th class="px-4 py-4">NAMA</th>
                    <th class="px-4 py-4">EMAIL</th>
                    <th class="px-4 py-4">ROLE</th>
                    <th class="px-4 py-4 text-right">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @php $no = 1; @endphp
                @forelse($this->users() as $user)
                    <tr class="hover:bg-surface-container-low transition-colors group">
                        <td class="px-4 py-4 text-body-md text-on-surface-variant">{{ $no++ }}</td>
                        <td class="px-4 py-4 text-body-md text-primary font-semibold">{{ $user->name }}</td>
                        <td class="px-4 py-4 text-body-md text-secondary">{{ $user->email }}</td>
                        <td class="px-4 py-4">
                            <span class="px-2 py-1 rounded-full text-label-sm font-bold {{ $user->role->value === 'super_admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $user->role->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-label-sm text-secondary hover:underline">Edit</a>
                                <button wire:click="confirmDelete({{ $user->id }})" class="text-label-sm text-error hover:underline">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-secondary text-body-md">Tidak ada user</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $this->users()->links() }}</div>

    @if($confirmDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-brand-navy/40 backdrop-blur-sm" wire:click="$set('confirmDelete', null)">
            <div class="bg-surface-container-lowest rounded-xl shadow-lg max-w-md w-full mx-4 p-6" onclick="event.stopPropagation()">
                <h3 class="text-headline-md text-primary mb-2">Konfirmasi Hapus</h3>
                <p class="text-body-md text-secondary mb-6">Apakah Anda yakin ingin menghapus user ini?</p>
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('confirmDelete', null)" class="px-6 py-2 border border-outline-variant rounded-lg text-label-md font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors">Batal</button>
                    <button wire:click="delete" class="px-6 py-2 bg-error text-on-error rounded-lg text-label-md font-bold hover:brightness-95 transition-all">Hapus</button>
                </div>
            </div>
        </div>
    @endif
</div>
