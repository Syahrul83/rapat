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
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-6">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">Jadwal Rapat</a>
                    <a href="{{ route('admin.meetings') }}" class="text-sm text-gray-600 hover:text-gray-900">Rapat</a>
                    <a href="{{ route('admin.reports') }}" class="text-sm text-gray-600 hover:text-gray-900">Laporan</a>
                    <a href="{{ route('admin.users') }}" class="text-sm text-blue-600 font-semibold">Users</a>
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

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Manajemen User</h1>
            <a href="{{ route('admin.users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">+ Tambah User</a>
        </div>

        <div class="mb-4">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari user..."
                class="w-full md:w-96 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $no = 1; @endphp
                    @forelse($this->users() as $user)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $no++ }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->role->value === 'super_admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $user->role->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-800">Edit</a>
                                <button wire:click="confirmDelete({{ $user->id }})" class="text-red-600 hover:text-red-800">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada user</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $this->users()->links() }}</div>

        @if($confirmDelete)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                    <h3 class="text-lg font-bold mb-4">Konfirmasi Hapus</h3>
                    <p class="mb-4">Apakah Anda yakin ingin menghapus user ini?</p>
                    <div class="flex justify-end space-x-2">
                        <button wire:click="$set('confirmDelete', null)" class="px-4 py-2 border rounded-md">Batal</button>
                        <button wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Hapus</button>
                    </div>
                </div>
            </div>
        @endif
    </main>
</div>
