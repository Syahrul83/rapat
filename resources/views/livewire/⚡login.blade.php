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

<div class="flex flex-col min-h-screen bg-background font-sans">
    <header class="bg-surface border-b border-outline-variant flex justify-between items-center w-full px-4 md:px-8 h-16">
        <div class="flex items-center gap-4">
            <a href="{{ route('home') }}" class="p-2 hover:bg-surface-container-low rounded-lg transition-colors">
                <span class="material-symbols-outlined text-primary">arrow_back</span>
            </a>
            <h1 class="text-headline-md font-headline-md font-bold text-primary">Sistem Administrasi</h1>
        </div>
        <div class="hidden md:flex gap-6 items-center">
            <span class="text-on-surface-variant text-label-md">Bantuan</span>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-[440px]">
            <div class="bg-surface-container-lowest rounded-lg border border-outline-variant/50 shadow-lg p-6 md:p-8" style="opacity: 1; transform: translateY(0px); transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);">
                <div class="flex flex-col items-center mb-8 text-center">
                    <div class="w-16 h-16 bg-primary-container rounded-xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-on-primary-container text-[32px]" style="font-variation-settings: 'FILL' 1;">admin_panel_settings</span>
                    </div>
                    <h2 class="text-headline-lg font-headline-lg text-on-surface">Masuk ke Panel</h2>
                    <p class="text-on-surface-variant text-body-sm mt-1">Silakan gunakan kredensial resmi instansi Anda.</p>
                </div>

                <form wire:submit="login" class="space-y-6">
                    <div class="space-y-1">
                        <label class="text-label-md text-on-surface block" for="email">Alamat Email</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-outline group-focus-within:text-primary transition-colors">mail</span>
                            </div>
                            <input wire:model.live="email" id="email" type="email" placeholder="nama@instansi.go.id"
                                class="block w-full pl-12 pr-4 py-3 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all text-body-md">
                        </div>
                        @error('email') <span class="text-error text-label-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <div class="flex justify-between items-center">
                            <label class="text-label-md text-on-surface block" for="password">Kata Sandi</label>
                            <a class="text-label-md text-primary hover:underline" href="#">Lupa Kata Sandi?</a>
                        </div>
                        <div class="relative group" x-data="{ show: false }">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-outline group-focus-within:text-primary transition-colors">lock</span>
                            </div>
                            <input wire:model.live="password" id="password" :type="show ? 'text' : 'password'" placeholder="••••••••"
                                class="block w-full pl-12 pr-12 py-3 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all text-body-md">
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-outline hover:text-on-surface transition-colors">
                                <span class="material-symbols-outlined" x-text="show ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                        </div>
                        @error('password') <span class="text-error text-label-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-4 p-4 bg-surface-container-low rounded-lg border border-outline-variant/20">
                        <span class="material-symbols-outlined text-secondary shrink-0">vpn_key</span>
                        <div class="space-y-0.5">
                            <p class="text-label-md text-on-surface">Keamanan Koneksi</p>
                            <p class="text-[12px] leading-[16px] text-on-surface-variant">Pastikan Anda terhubung ke jaringan VPN Instansi yang terenkripsi sebelum mengakses portal ini.</p>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 px-6 bg-primary text-on-primary font-bold rounded-lg shadow-sm hover:brightness-95 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                        <span>Masuk Ke Panel</span>
                        <span class="material-symbols-outlined text-[18px]">login</span>
                    </button>
                </form>
            </div>

            <p class="text-center mt-8 text-on-surface-variant text-body-sm">
                Kesulitan mengakses akun? <a href="#" class="text-primary font-bold hover:underline">Hubungi Helpdesk IT</a>
            </p>
        </div>
    </main>

    <footer class="w-full py-6 px-4 md:px-8 mt-auto bg-surface-container-low border-t border-outline-variant">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex flex-col items-center md:items-start gap-1">
                <span class="text-label-md text-secondary">&copy; 2024 Sistem Administrasi Digital. Seluruh Hak Cipta Dilindungi.</span>
            </div>
            <div class="flex gap-6">
                <a class="text-label-md text-on-surface-variant hover:text-primary transition-colors" href="#">Kebijakan Privasi</a>
                <a class="text-label-md text-on-surface-variant hover:text-primary transition-colors" href="#">Syarat &amp; Ketentuan</a>
                <a class="text-label-md text-on-surface-variant hover:text-primary transition-colors" href="#">Bantuan</a>
            </div>
        </div>
    </footer>
</div>
