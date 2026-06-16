<?php

namespace App\Services;

use App\Models\Meeting;
use App\Models\Participant;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getPerKegiatan(?int $meetingId = null): array
    {
        $query = Meeting::query()
            ->where('status', 'published')
            ->withCount([
                'participants as total_participants',
                'participants as narasumber_count' => fn($q) => $q->where('tipe_peserta', 'narasumber'),
                'participants as peserta_count' => fn($q) => $q->where('tipe_peserta', 'peserta'),
            ])
            ->with('meetingDays');

        if ($meetingId) {
            $query->where('id', $meetingId);
        }

        return $query->orderByDesc('created_at')->get()->map(fn($m) => [
            'id' => $m->id,
            'title' => $m->title,
            'dates' => $m->meetingDays->pluck('date')->map(fn($d) => $d->format('d M Y'))->implode(', '),
            'location' => $m->location,
            'total_participants' => $m->total_participants,
            'narasumber_count' => $m->narasumber_count,
            'peserta_count' => $m->peserta_count,
        ])->toArray();
    }

    public function getPerBulan(int $year, ?int $month = null): array
    {
        $query = DB::table('meetings')
            ->join('meeting_days', 'meetings.id', '=', 'meeting_days.meeting_id')
            ->leftJoin('participants', 'meetings.id', '=', 'participants.meeting_id')
            ->where('meetings.status', 'published')
            ->whereYear('meeting_days.date', $year);

        if ($month) {
            $query->whereMonth('meeting_days.date', $month);
        }

        $driver = DB::getDriverName();
        $monthExpr = $driver === 'sqlite' ? "strftime('%m', meeting_days.date)" : 'MONTH(meeting_days.date)';

        return $query
            ->select(
                DB::raw("{$monthExpr} as bulan"),
                DB::raw('COUNT(DISTINCT meetings.id) as jumlah_rapat'),
                DB::raw('COUNT(DISTINCT participants.id) as jumlah_peserta')
            )
            ->groupBy(DB::raw("{$monthExpr}"))
            ->orderBy('bulan')
            ->get()
            ->map(fn($row) => [
                'bulan' => (int) $row->bulan,
                'nama_bulan' => \Carbon\Carbon::createFromDate($year, $row->bulan, 1)->translatedFormat('F'),
                'jumlah_rapat' => $row->jumlah_rapat,
                'jumlah_peserta' => $row->jumlah_peserta,
            ])
            ->toArray();
    }

    public function getPerTahun(int $year): array
    {
        $result = DB::table('meetings')
            ->join('meeting_days', 'meetings.id', '=', 'meeting_days.meeting_id')
            ->leftJoin('participants', 'meetings.id', '=', 'participants.meeting_id')
            ->where('meetings.status', 'published')
            ->whereYear('meeting_days.date', $year)
            ->select(
                DB::raw('COUNT(DISTINCT meetings.id) as jumlah_rapat'),
                DB::raw('COUNT(DISTINCT participants.id) as jumlah_peserta')
            )
            ->first();

        return [
            'tahun' => $year,
            'jumlah_rapat' => $result->jumlah_rapat ?? 0,
            'jumlah_peserta' => $result->jumlah_peserta ?? 0,
        ];
    }
}
