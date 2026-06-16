<?php

use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\User;
use App\Enums\UserRole;

new class extends Component {
    public $userId;
    #[Validate('required|max:255')]
    public $name = '';
    #[Validate('required|email|max:255|unique:users,email,{userId}')]
    public $email = '';
    public $password = '';
    #[Validate('required|in:super_admin,admin_tu')]
    public $role = 'admin_tu';

    public function mount($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role->value;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if ($this->password) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($this->password);
        }

        User::findOrFail($this->userId)->update($data);

        session()->flash('success', 'User berhasil diperbarui');
        return redirect()->route('admin.users');
    }
};
?>

<div>
    <div class="max-w-lg">
        <a href="{{ route('admin.users') }}" class="text-label-md text-primary hover:underline mb-4 inline-flex items-center gap-1">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
        </a>
        <h1 class="text-headline-md text-primary mb-6">Edit User</h1>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
            <form wire:submit="save" class="space-y-5">
                <div>
                    <label class="block text-label-md text-on-surface mb-1">Nama *</label>
                    <input wire:model.live="name" type="text"
                        class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
                    @error('name') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-label-md text-on-surface mb-1">Email *</label>
                    <input wire:model.live="email" type="email"
                        class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
                    @error('email') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-label-md text-on-surface mb-1">Password <span class="text-on-surface-variant">(kosongkan jika tidak diubah)</span></label>
                    <input wire:model.live="password" type="password"
                        class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
                    @error('password') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-label-md text-on-surface mb-1">Role *</label>
                    <select wire:model.live="role"
                        class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
                        <option value="admin_tu">Admin TU</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                    @error('role') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('admin.users') }}" class="px-6 py-2 border border-outline-variant rounded-lg text-label-md font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-primary text-on-primary rounded-lg text-label-md font-bold hover:brightness-95 transition-all">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
