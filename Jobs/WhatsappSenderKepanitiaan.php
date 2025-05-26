<?php
namespace Modules\Rapat\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Rapat\Entities\Kepanitiaan;
use Modules\Rapat\Http\Service\Implementation\WhatsappService;

class WhatsappSenderKepanitiaan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $kepanitiaan;
    private $type;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Kepanitiaan $kepanitiaan, $type)
    {
        $this->kepanitiaan = $kepanitiaan;
        $this->type        = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(WhatsappService $whatsappService)
    {
        $this->kepanitiaan->load(['pegawai', 'ketua']);
        $whatsappService->sendMessageKepanitiaan($this->kepanitiaan, $this->type);
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
