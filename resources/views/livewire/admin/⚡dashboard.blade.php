<?php

use Livewire\Component;
use App\Models\Meeting;
use App\Models\Participant;
use App\Models\User;
use App\Enums\MeetingStatus;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public function getTotalMeetings()
    {
        return Meeting::count();
    }

    public function getTotalParticipants()
    {
        return Participant::count();
    }

    public function getPendingVerification()
    {
        return Meeting::where('status', MeetingStatus::Draft)->count();
    }

    public function getAvgAttendance()
    {
        $totalMeetings = Meeting::where('status', MeetingStatus::Published)->count();
        if ($totalMeetings === 0) {
            return 0;
        }
        $totalParticipants = Participant::count();
        $avg = ($totalParticipants / ($totalMeetings * 20)) * 100;
        return round(min($avg, 100), 1);
    }

    public function getChartData()
    {
        $days = [];
        $labels = [];
        $maxCount = 1;

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->translatedFormat('D');
            $count = Participant::whereDate('registered_at', $date->toDateString())->count();
            $days[] = $count;
            if ($count > $maxCount) {
                $maxCount = $count;
            }
        }

        return [
            'labels' => $labels,
            'data' => $days,
            'max' => $maxCount,
            'today' => 6,
        ];
    }

    public function getActiveStaff()
    {
        return User::whereIn('role', [\App\Enums\UserRole::SuperAdmin, \App\Enums\UserRole::AdminTu])
            ->get()
            ->map(fn($u) => [
                'name' => $u->name,
                'email' => $u->email,
                'initials' => collect(explode(' ', $u->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                'role' => $u->role === \App\Enums\UserRole::SuperAdmin ? 'Super Admin' : 'Admin TU',
                'online' => true,
            ]);
    }

    public function getRecentMeetings()
    {
        return Meeting::withCount('participants')
            ->with('meetingDays')
            ->orderByDesc('created_at')
            ->limit(4)
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'title' => $m->title,
                'date' => $m->meetingDays->sortBy('date')->first()?->date->format('d M Y'),
                'time' => substr($m->start_time, 0, 5) . ' WITA',
                'participants' => $m->participants_count . ' Orang',
                'status' => $m->status === MeetingStatus::Draft ? 'Menunggu' : ($m->status === MeetingStatus::Published ? 'Terjadwal' : 'Selesai'),
                'status_color' => $m->status === MeetingStatus::Draft ? 'yellow' : ($m->status === MeetingStatus::Published ? 'blue' : 'green'),
            ]);
    }
};
?>

<div>
    <section class="mb-8">
        <h1 class="text-display-lg text-primary mb-1">Selamat Datang, {{ auth()->user()->name }}!</h1>
        <p class="text-body-lg text-secondary">Pantau seluruh aktivitas layanan digital kementerian dalam satu dashboard terpadu.</p>
    </section>

    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-3">
                <div class="p-2 bg-primary-container text-on-primary-container rounded-lg">
                    <span class="material-symbols-outlined">groups</span>
                </div>
                <span class="text-label-sm font-bold text-green-600 bg-green-50 px-2 py-1 rounded">+{{ $this->getTotalMeetings() > 0 ? '100' : '0' }}%</span>
            </div>
            <p class="text-label-md text-secondary mb-1">Total Rapat</p>
            <h3 class="text-headline-md text-primary">{{ $this->getTotalMeetings() }}</h3>
        </div>

        <div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-3">
                <div class="p-2 bg-secondary-container text-on-secondary-container rounded-lg">
                    <span class="material-symbols-outlined">person_add</span>
                </div>
                <span class="text-label-sm font-bold text-green-600 bg-green-50 px-2 py-1 rounded">+5%</span>
            </div>
            <p class="text-label-md text-secondary mb-1">Total Peserta</p>
            <h3 class="text-headline-md text-primary">{{ $this->getTotalParticipants() }}</h3>
        </div>

        <div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-3">
                <div class="p-2 bg-error-container text-on-error-container rounded-lg">
                    <span class="material-symbols-outlined">pending_actions</span>
                </div>
                <span class="text-label-sm font-bold text-error bg-red-50 px-2 py-1 rounded">Urgent</span>
            </div>
            <p class="text-label-md text-secondary mb-1">Rapat Menunggu Verifikasi</p>
            <h3 class="text-headline-md text-primary">{{ $this->getPendingVerification() }}</h3>
        </div>

        <div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-3">
                <div class="p-2 bg-surface-variant text-on-surface-variant rounded-lg">
                    <span class="material-symbols-outlined">check_circle</span>
                </div>
                <span class="text-label-sm font-bold bg-surface-container-high px-2 py-1 rounded">Stabil</span>
            </div>
            <p class="text-label-md text-secondary mb-1">Rata-rata Kehadiran</p>
            <h3 class="text-headline-md text-primary">{{ $this->getAvgAttendance() }}%</h3>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-surface-container-lowest border border-outline-variant rounded-xl p-4 flex flex-col h-full">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h4 class="text-headline-md text-primary">Statistik Pendaftaran Peserta</h4>
                    <p class="text-label-md text-secondary">Aktivitas pendaftaran 7 hari terakhir</p>
                </div>
            </div>
            @php $chart = $this->getChartData(); @endphp
            <div class="flex-1 flex items-end justify-between gap-2 h-64 px-2 border-b border-outline-variant pb-1">
                @foreach($chart['labels'] as $i => $label)
                    @php
                        $pct = $chart['max'] > 0 ? ($chart['data'][$i] / $chart['max']) * 100 : 0;
                        $isToday = $i === $chart['today'];
                    @endphp
                    <div class="flex flex-col items-center flex-1 gap-1">
                        <div class="chart-bar w-full rounded-t-lg {{ $isToday ? 'bg-primary' : 'bg-primary-container' }}" style="height: {{ $pct }}%;"></div>
                        <span class="text-label-sm text-secondary {{ $isToday ? 'font-bold' : '' }}">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-1 bg-surface-container-lowest border border-outline-variant rounded-xl p-4 flex flex-col">
            <h4 class="text-headline-md text-primary mb-4">Petugas Aktif</h4>
            <div class="space-y-4">
                @forelse($this->getActiveStaff() as $staff)
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container font-bold text-sm">{{ $staff['initials'] }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-label-md font-bold text-primary truncate">{{ $staff['name'] }}</p>
                            <p class="text-label-sm text-secondary truncate">{{ $staff['role'] }}</p>
                        </div>
                        <span class="w-2 h-2 rounded-full {{ $staff['online'] ? 'bg-green-500' : 'bg-outline-variant' }} shrink-0"></span>
                    </div>
                @empty
                    <p class="text-label-md text-secondary">Belum ada petugas.</p>
                @endforelse
            </div>
            <button class="mt-auto w-full py-2 border border-primary text-primary text-label-md rounded-lg hover:bg-primary-container hover:text-on-primary-container transition-colors">Lihat Semua Petugas</button>
        </div>

        <div class="lg:col-span-3 bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden">
            <div class="p-4 border-b border-outline-variant flex justify-between items-center">
                <h4 class="text-headline-md text-primary">Rapat Terbaru</h4>
                <a href="{{ route('admin.meetings') }}" class="text-primary text-label-md hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-surface-container-low text-primary text-label-md border-b border-outline-variant">
                        <tr>
                            <th class="px-4 py-4">Nama Kegiatan</th>
                            <th class="px-4 py-4">Waktu &amp; Tanggal</th>
                            <th class="px-4 py-4">Peserta</th>
                            <th class="px-4 py-4">Status</th>
                            <th class="px-4 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @forelse($this->getRecentMeetings() as $meeting)
                            <tr class="hover:bg-surface-container-low transition-colors group">
                                <td class="px-4 py-4 text-body-md text-primary font-semibold">{{ $meeting['title'] }}</td>
                                <td class="px-4 py-4 text-body-md text-secondary">{{ $meeting['date'] }}, {{ $meeting['time'] }}</td>
                                <td class="px-4 py-4 text-body-md text-secondary">{{ $meeting['participants'] }}</td>
                                <td class="px-4 py-4">
                                    <span class="px-2 py-1 rounded-full text-label-sm font-bold
                                        {{ $meeting['status_color'] === 'green' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $meeting['status_color'] === 'blue' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $meeting['status_color'] === 'yellow' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                                        {{ $meeting['status'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <a href="{{ route('admin.meetings.edit', $meeting['id']) }}" class="px-3 py-1.5 bg-primary text-on-primary rounded-lg text-label-sm font-bold hover:brightness-95 transition-all inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[16px]">edit</span> Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-secondary text-body-md">Belum ada rapat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
