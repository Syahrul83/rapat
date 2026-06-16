<?php

use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;

new class extends Component {
    #[Validate('required|max:255')]
    public $name = '';

    #[Validate('required|email|max:255|unique:users,email')]
    public $email = '';

    #[Validate('required|min:8')]
    public $password = '';

    #[Validate('required|in:super_admin,admin_tu')]
    public $role = 'admin_tu';

    public function save()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
        ]);

        session()->flash('success', 'User berhasil ditambahkan');
        return redirect()->route('admin.users');
    }
};
?>

<div>
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-6">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">Jadwal Rapat</a>
                    <a href="{{ route('admin.users') }}" class="text-sm text-blue-600 font-semibold">Users</a>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold mb-6">Tambah User</h1>

        <div class="bg-white rounded-lg shadow p-6">
            <form wire:submit="save">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                    <input wire:model.live="name" type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input wire:model.live="email" type="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                    <input wire:model.live="password" type="password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                    <select wire:model.live="role"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="admin_tu">Admin TU</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                    @error('role') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('admin.users') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </main>
</div>
