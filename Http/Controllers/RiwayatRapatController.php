<?php
namespace Modules\Rapat\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Rapat\Entities\RapatAgenda;
use Modules\Rapat\Http\Helper\StatusAgendaRapat;

class RiwayatRapatController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $rapats = RapatAgenda::pegawaiIsPesertaOrCreator(Auth::user()->pegawai->id);
        $rapats = $rapats->when($request->input('cari'), function ($query, $cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('agenda_rapat', 'like', "%$cari%");
            });
        })
            ->when($request->input('dari_tgl'), function ($query, $dari) {
                $query->whereDate('waktu_mulai', '>=', $dari);
            })
            ->when($request->input('sampai_tgl'), function ($query, $sampai) {
                $query->whereDate('waktu_mulai', '<=', $sampai);
            })
            ->when($request->input('filter'), function ($query, $filter) {
                if ($filter === 'minggu') {
                    $query->whereBetween('waktu_mulai', [
                        Carbon::now('Asia/Jakarta')->startOfWeek(),
                        Carbon::now('Asia/Jakarta')->endOfWeek(),
                    ]);
                } elseif ($filter === 'bulan') {
                    $query->whereBetween('waktu_mulai', [
                        Carbon::now('Asia/Jakarta')->startOfMonth(),
                        Carbon::now('Asia/Jakarta')->endOfMonth(),
                    ]);
                }
            })
            ->where('status', StatusAgendaRapat::COMPLETED->value)
            ->orderBy('created_at', 'desc')
            ->paginate(10)->withQueryString();
        return view('rapat::rapat.riwayat.index', [
            'rapats' => $rapats,
        ]);
    }

}
