<?php
namespace Modules\Rapat\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Rapat\Entities\RapatAgenda;

class ExportPdfController extends Controller
{
    public function generateNotulenRapat(RapatAgenda $rapatAgenda)
    {
        $rapatAgenda->load(['rapatAgendaPimpinan', 'rapatAgendaNotulis', 'rapatAgendaPeserta', 'rapatTindakLanjut.pegawai', 'rapatLampiran', 'rapatNotulen.notulenFiles', 'rapatDokumentasi']);
        return view('rapat::rapat.pdf.notulen', ['rapat' => $rapatAgenda]);

    }
}
