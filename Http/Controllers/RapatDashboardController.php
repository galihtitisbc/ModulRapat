<?php
namespace Modules\Rapat\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Rapat\Entities\Pegawai;
use Modules\Rapat\Entities\RapatAgenda;
use Modules\Rapat\Http\Helper\StatusAgendaRapat;
use Modules\Rapat\Http\Helper\StatusPesertaRapat;
use Modules\Rapat\Http\Helper\StatusTindakLanjut;

class RapatDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $tahunSekarang       = date('Y');
        $pegawai_id          = Auth::user()->pegawai->id;
        $totalRapatMendatang = RapatAgenda::whereHas('rapatAgendaPeserta', function ($q) use ($pegawai_id) {
            $q->where('pegawai_id', $pegawai_id);
        })->whereYear('waktu_mulai', $tahunSekarang)
            ->where(DB::raw('DATE(waktu_mulai)'), '>=', date('Y-m-d'))
            ->where('status', StatusAgendaRapat::SCHEDULED->value)
            ->orderBy('waktu_mulai', 'asc')
            ->get();
        $totalHadirRapat = Pegawai::where('id', $pegawai_id)
            ->withCount(['rapatAgendaPeserta as hadir_count' => function ($query) {
                $query->where('rapat_pesertas.status', StatusPesertaRapat::HADIR->value);
            }])->with(['rapatAgendaPeserta' => function ($query) use ($tahunSekarang) {
            $query->whereYear('rapat_agendas.waktu_mulai', $tahunSekarang);
        }])
            ->first();
        $totalKeseluruhanRapat = Pegawai::where('id', $pegawai_id)->withCount(['rapatAgendaPeserta' => function ($query) use ($tahunSekarang) {
            $query->whereYear('rapat_agendas.waktu_mulai', $tahunSekarang);
        }])
            ->first();
        $totalTugas = Pegawai::where('id', $pegawai_id)->withCount(['rapatTindakLanjut' => function ($query) use ($tahunSekarang) {
            $query->whereYear('rapat_tindak_lanjuts.created_at', $tahunSekarang);
        }])
            ->first();
        $totalTugasSelesai = Pegawai::where('id', $pegawai_id)->withCount(['rapatTindakLanjut' => function ($q) use ($tahunSekarang) {
            $q->where('status', StatusTindakLanjut::SELESAI->value)
                ->whereYear('rapat_tindak_lanjuts.created_at', $tahunSekarang);
        }])
            ->first();
        $tugasMendatang = Pegawai::where('id', $pegawai_id)->with(['rapatTindakLanjut' => function ($q) use ($tahunSekarang) {
            $q->where('status', StatusTindakLanjut::BELUM_SELESAI->value)
                ->whereYear('rapat_tindak_lanjuts.created_at', $tahunSekarang)
                ->orderBy('batas_waktu', 'asc');
        }])->first();
        return view('rapat::index', [
            'totalRapatMendatang'   => optional($totalRapatMendatang)->count() ?? 0,
            'rapat'                 => optional($totalRapatMendatang)->first(),
            'totalHadirRapat'       => optional($totalHadirRapat)->hadir_count ?? 0,
            'totalKeseluruhanRapat' => optional($totalKeseluruhanRapat)->rapat_agenda_peserta_count ?? 0,
            'totalTugas'            => optional($totalTugas)->rapat_tindak_lanjut_count ?? 0,
            'tugasMendatang'        => optional(optional($tugasMendatang)->rapatTindakLanjut)->first(),
            'totalTugasSelesai'     => optional($totalTugasSelesai)->rapat_tindak_lanjut_count ?? 0,
        ]);
    }

}
