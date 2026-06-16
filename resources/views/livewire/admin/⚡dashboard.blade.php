<?php

use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Meeting;
use App\Models\Participant;

new class extends Component {
    public function logout()
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    }

    public function getTotalMeetings()
    {
        return Meeting::count();
    }

    public function getTotalParticipants()
    {
        return Participant::count();
    }

    public function getTodayMeetings()
    {
        return Meeting::whereHas('meetingDays', function ($q) {
            $q->where('date', today());
        })->count();
    }
};
?>

<div>
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-6">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">Jadwal Rapat - Admin</a>
                    <a href="{{ route('admin.meetings') }}" class="text-sm text-gray-600 hover:text-gray-900">Rapat</a>
                    <a href="{{ route('admin.reports') }}" class="text-sm text-gray-600 hover:text-gray-900">Laporan</a>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">{{ auth()->user()->name }} ({{ auth()->user()->role->label() }})</span>
                    <button wire:click="logout" class="text-sm text-red-600 hover:text-red-800">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700">Total Rapat</h3>
                <p class="text-3xl font-bold text-blue-600">{{ $this->getTotalMeetings() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700">Total Peserta</h3>
                <p class="text-3xl font-bold text-green-600">{{ $this->getTotalParticipants() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700">Rapat Hari Ini</h3>
                <p class="text-3xl font-bold text-orange-600">{{ $this->getTodayMeetings() }}</p>
            </div>
        </div>

        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Selamat Datang, {{ auth()->user()->name }}!</h2>
            <p class="text-gray-600">Gunakan menu di atas untuk mengelola jadwal rapat.</p>
        </div>
    </main>
</div>
