<?php

namespace App\Actions\Meeting;

use App\Enums\MeetingStatus;
use App\Models\Meeting;

class PublishMeetingAction
{
    public function execute(int $id): void
    {
        $meeting = Meeting::findOrFail($id);
        $meeting->update(['status' => MeetingStatus::Published]);
    }
}
