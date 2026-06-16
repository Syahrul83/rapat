<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function participantList(Meeting $meeting)
    {
        $meeting->load(['meetingDays', 'participants']);

        $pdf = Pdf::loadView('pdf.participant-list', compact('meeting'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("daftar-hadir-{$meeting->title}.pdf");
    }
}
