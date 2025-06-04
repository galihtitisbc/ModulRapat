<?php
namespace Modules\Rapat\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Rapat\Entities\RapatAgenda;
use Modules\Rapat\Http\Helper\FlashMessage;
use Modules\Rapat\Http\Requests\UploadNotulenRequest;
use Modules\Rapat\Http\Service\Implementation\NotulisService;

class NotulisController extends Controller
{
    private $notulisService;
    public function __construct(NotulisService $notulisService)
    {
        $this->notulisService = $notulisService;
    }
    public function formUnggahNotulen(RapatAgenda $rapatAgenda)
    {
        $rapat = $rapatAgenda->load(['rapatAgendaPimpinan', 'rapatAgendaNotulis', 'rapatAgendaPeserta']);

        return view('rapat::rapat.notulis.unggah-notulen', [
            'agendaRapat' => $rapat,
        ]);
    }
    public function storeNotulen(RapatAgenda $rapatAgenda, UploadNotulenRequest $request)
    {
        $validated = $request->validated();
        try {
            $this->notulisService->storeNotulen($rapatAgenda, $validated);
            FlashMessage::success('Notulen Berhasil Unggah');
            return redirect()->to('/rapat/agenda-rapat');
        } catch (\Throwable $th) {
            FlashMessage::success('Notulen Gagal Di Unggah');
            return redirect()->to('/rapat/agenda-rapat');
        }
    }
    public function downloadNotulen($file)
    {
        try {
            return Storage::download('/public/notulen/' . $file);
        } catch (\Throwable $th) {
            return "File Tidak Ditemukan";
        }
    }
}
