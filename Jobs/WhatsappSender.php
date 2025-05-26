<?php
namespace Modules\Rapat\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Rapat\Entities\RapatAgenda;
use Modules\Rapat\Http\Service\Implementation\WhatsappService;

class WhatsappSender implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $agendaRapat;
    private $type;
    private $status;
    private $tindakLanjut;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(RapatAgenda $agendaRapat, $type, $status, $tindakLanjut = null)
    {
        $this->agendaRapat  = $agendaRapat;
        $this->type         = $type;
        $this->status       = $status;
        $this->tindakLanjut = $tindakLanjut;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(WhatsappService $whatsappService)
    {
        if ($this->type === 'rapat') {
            $whatsappService->sendMessageRapat($this->agendaRapat, $this->status);
        } else if ($this->type === 'penugasan') {
            $whatsappService->sendMessagePenugasan($this->agendaRapat, $this->tindakLanjut, 'penugasan');
        } else if ($this->type == 'penilaian') {
            $whatsappService->sendMessagePenilaian($this->agendaRapat, $this->tindakLanjut, 'penilaian');
        }
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
