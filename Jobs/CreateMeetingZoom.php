<?php
namespace Modules\Rapat\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Rapat\Entities\RapatAgenda;
use Modules\Rapat\Http\Service\MeetingServiceInterface;

class CreateMeetingZoom implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries   = 3;
    public $timeout = 30;
    public function backoff(): int
    {
        return 10; // jeda antar retry dalam detik
    }
    protected $agendaRapat;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(RapatAgenda $agendaRapat)
    {
        $this->agendaRapat = $agendaRapat;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MeetingServiceInterface $zoomService)
    {
        $zoomService->createMeeting($this->agendaRapat);
    }
    public function failed(\Throwable $exception)
    {
        Log::error('Job gagal: ' . $exception->getMessage(), [
            'trace' => $exception->getTraceAsString(),
            'job'   => self::class,
            'data'  => $this->yourJobData ?? null,
        ]);
    }
}
