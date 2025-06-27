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
        $username            = Auth::user()->username;
        $totalRapatMendatang = RapatAgenda::whereHas('rapatAgendaPeserta', function ($q) use ($username) {
            $q->where('pegawai_username', $username);
        })->whereYear('waktu_mulai', $tahunSekarang)
            ->where(DB::raw('DATE(waktu_mulai)'), '>=', date('Y-m-d'))
            ->where('status', StatusAgendaRapat::SCHEDULED->value)
            ->orderBy('waktu_mulai', 'asc')
            ->get();
        $totalHadirRapat = Pegawai::where('username', $username)
            ->withCount(['rapatAgendaPeserta as hadir_count' => function ($query) {
                $query->where('rapat_pesertas.status', StatusPesertaRapat::HADIR->value);
            }])->with(['rapatAgendaPeserta' => function ($query) use ($tahunSekarang) {
            $query->whereYear('rapat_agendas.waktu_mulai', $tahunSekarang);
        }])
            ->first();
        $totalKeseluruhanRapat = Pegawai::where('username', $username)->withCount(['rapatAgendaPeserta' => function ($query) use ($tahunSekarang) {
            $query->whereYear('rapat_agendas.waktu_mulai', $tahunSekarang);
        }])
            ->first();
        $totalTugas = Pegawai::where('username', $username)->withCount(['rapatTindakLanjut' => function ($query) use ($tahunSekarang) {
            $query->whereYear('rapat_tindak_lanjuts.created_at', $tahunSekarang);
        }])
            ->first();
        $totalTugasSelesai = Pegawai::where('username', $username)->withCount(['rapatTindakLanjut' => function ($q) use ($tahunSekarang) {
            $q->where('status', StatusTindakLanjut::SELESAI->value)
                ->whereYear('rapat_tindak_lanjuts.created_at', $tahunSekarang);
        }])
            ->first();
        $tugasMendatang = Pegawai::where('username', $username)->with(['rapatTindakLanjut' => function ($q) use ($tahunSekarang) {
            $q->where('status', StatusTindakLanjut::BELUM_SELESAI->value)
                ->whereYear('rapat_tindak_lanjuts.created_at', $tahunSekarang)
                ->orderBy('batas_waktu', 'asc');
        }])->first();
        return view('rapat::index', [
            'totalRapatMendatang'   => $totalRapatMendatang->count(),
            'rapat'                 => $totalRapatMendatang->first(),
            'totalHadirRapat'       => $totalHadirRapat->hadir_count,
            'totalKeseluruhanRapat' => $totalKeseluruhanRapat->rapat_agenda_peserta_count,
            'totalTugas'            => $totalTugas->rapat_tindak_lanjut_count,
            'tugasMendatang'        => $tugasMendatang->rapatTindakLanjut->first(),
            'totalTugasSelesai'     => $totalTugasSelesai->rapat_tindak_lanjut_count,
        ]);
    }

}
