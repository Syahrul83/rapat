<?php

namespace Database\Seeders;

use App\Enums\MeetingStatus;
use App\Models\Meeting;
use App\Models\MeetingDay;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Database\Seeder;

class MeetingSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'super@admin.test')->first();

        $meetings = [
            [
                'title' => 'Rapat Koordinasi Bulanan',
                'description' => 'Rapat koordinasi bulanan seluruh pegawai untuk membahas target dan capaian kuartal ini.',
                'location' => 'Ruang Rapat Utama Lt. 2',
                'start_time' => '09:00',
                'end_time' => '12:00',
                'status' => MeetingStatus::Published,
                'dates' => [now()->addDays(3)->toDateString()],
            ],
            [
                'title' => 'Sosialisasi Kebijakan Baru',
                'description' => 'Sosialisasi peraturan terbaru dari Kementerian terkait perubahan SOP.',
                'location' => 'Aula Gedung A',
                'start_time' => '13:00',
                'end_time' => '16:00',
                'status' => MeetingStatus::Published,
                'dates' => [now()->addDays(5)->toDateString(), now()->addDays(6)->toDateString()],
            ],
            [
                'title' => 'Workshop Digitalisasi Arsip',
                'description' => 'Pelatihan penggunaan sistem digitalisasi arsip untuk seluruh admin unit kerja.',
                'location' => 'Lab Komputer Lt. 3',
                'start_time' => '08:30',
                'end_time' => '16:00',
                'status' => MeetingStatus::Published,
                'dates' => [now()->addDays(7)->toDateString(), now()->addDays(8)->toDateString(), now()->addDays(9)->toDateString()],
            ],
            [
                'title' => 'Rapat Anggaran Tahunan',
                'description' => 'Pembahasan RKA-KL tahun anggaran berikutnya bersama seluruh pimpinan.',
                'location' => 'Ruang Rapat VIP',
                'start_time' => '10:00',
                'end_time' => '15:00',
                'status' => MeetingStatus::Draft,
                'dates' => [now()->addDays(14)->toDateString()],
            ],
            [
                'title' => 'Evaluasi Kinerja Semester',
                'description' => 'Evaluasi kinerja seluruh unit kerja selama semester pertama.',
                'location' => 'Ruang Rapat Utama Lt. 2',
                'start_time' => '09:00',
                'end_time' => '12:00',
                'status' => MeetingStatus::Published,
                'dates' => [now()->subDay()->toDateString()],
            ],
        ];

        foreach ($meetings as $data) {
            $dates = $data['dates'];
            unset($data['dates']);

            $meeting = Meeting::create(array_merge($data, [
                'created_by' => $admin->id,
            ]));

            foreach ($dates as $date) {
                MeetingDay::create([
                    'meeting_id' => $meeting->id,
                    'date' => $date,
                ]);
            }
        }

        $publishedMeetings = Meeting::where('status', MeetingStatus::Published)->get();
        $participantNames = [
            ['name' => 'Budi Santoso', 'jenis' => 'pegawai_dinas', 'nip' => '198501152010011001'],
            ['name' => 'Siti Rahayu', 'jenis' => 'pegawai_dinas', 'nip' => '199001252015012002'],
            ['name' => 'Ahmad Hidayat', 'jenis' => 'pegawai_dinas', 'nip' => '198703102009011003'],
            ['name' => 'Dewi Lestari', 'jenis' => 'eksternal', 'nik' => '3201234567890001'],
            ['name' => 'Rudi Hermawan', 'jenis' => 'pegawai_dinas', 'nip' => '199205052018011004'],
            ['name' => 'Maya Putri', 'jenis' => 'eksternal', 'nik' => '3201234567890002'],
            ['name' => 'Andi Prasetyo', 'jenis' => 'pegawai_dinas', 'nip' => '198801082012011005'],
            ['name' => 'Rina Wati', 'jenis' => 'pegawai_dinas', 'nip' => '199507122020012006'],
        ];

        $tipeOptions = ['narasumber', 'peserta'];

        foreach ($publishedMeetings as $meeting) {
            $shuffled = $participantNames;
            shuffle($shuffled);
            $count = rand(3, 6);

            for ($i = 0; $i < $count; $i++) {
                $p = $shuffled[$i];
                Participant::create([
                    'meeting_id' => $meeting->id,
                    'name' => $p['name'],
                    'jenis_peserta' => $p['jenis'],
                    'tipe_peserta' => $tipeOptions[array_rand($tipeOptions)],
                    'nip' => $p['nip'] ?? null,
                    'nik' => $p['nik'] ?? null,
                    'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
                    'declaration' => true,
                ]);
            }
        }
    }
}
