<?php

use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    #[Validate('required|email')]
    public $email = '';

    #[Validate('required')]
    public $password = '';

    public function login()
    {
        $this->validate();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], true)) {
            $this->addError('email', 'Email atau password salah.');
            return;
        }

        $user = Auth::user();

        if ($user->role !== UserRole::SuperAdmin && $user->role !== UserRole::AdminTu) {
            Auth::logout();
            $this->addError('email', 'Anda tidak memiliki akses.');
            return;
        }

        session()->regenerate();

        return redirect()->route('admin.dashboard');
    }
};
?>

<div>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-md p-8">
                <h1 class="text-2xl font-bold text-center mb-6">Login Admin</h1>

                <form wire:submit="login">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input wire:model.live="email" type="email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input wire:model.live="password" type="password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
