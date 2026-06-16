<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function participantList(Meeting $meeting, Request $request)
    {
        $meeting->load('meetingDays');

        $participantsQuery = $meeting->participants();

        if ($request->filled('date')) {
            $participantsQuery->whereDate('registered_at', $request->date);
        }

        $participants = $participantsQuery->orderBy('registered_at')->get();

        $pdf = Pdf::loadView('pdf.participant-list', compact('meeting', 'participants'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("daftar-hadir-{$meeting->title}.pdf");
    }

    public function reportPdf(Request $request)
    {
        $service = app(ReportService::class);
        $filterType = $request->get('type', 'kegiatan');
        $filterYear = $request->get('year', date('Y'));
        $filterMonth = $request->get('month');
        $filterMeeting = $request->get('meeting');

        $report = match ($filterType) {
            'kegiatan' => $service->getPerKegiatan($filterMeeting ? (int) $filterMeeting : null),
            'bulanan' => $service->getPerBulan((int) $filterYear, $filterMonth ? (int) $filterMonth : null),
            'tahunan' => [$service->getPerTahun((int) $filterYear)],
            default => [],
        };

        $pdf = Pdf::loadView('pdf.report', compact('report', 'filterType', 'filterYear', 'filterMonth', 'filterMeeting'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("laporan-rekapitulasi.pdf");
    }
}
