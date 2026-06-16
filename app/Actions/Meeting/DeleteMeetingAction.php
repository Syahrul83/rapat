<?php

namespace App\Actions\Meeting;

use App\Models\Meeting;

class DeleteMeetingAction
{
    public function execute(int $id): void
    {
        $meeting = Meeting::findOrFail($id);
        $meeting->delete();
    }
}
