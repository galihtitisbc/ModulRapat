<?php
namespace Modules\Rapat\Http\Service;

interface MeetingServiceInterface
{
    public function authentication();
    public function createMeeting($data);
}
