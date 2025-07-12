<?php
namespace Modules\Rapat\Http\Controllers;

use App\Models\Core\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Modules\Rapat\Entities\Kepanitiaan;
use Modules\Rapat\Entities\Pegawai;
use Modules\Rapat\Entities\RapatAgenda;
use Modules\Rapat\Http\Helper\FlashMessage;
use Modules\Rapat\Http\Helper\RoleGroupHelper;
use Modules\Rapat\Http\Helper\StatusAgendaRapat;
use Modules\Rapat\Http\Helper\StatusPesertaRapat;
use Modules\Rapat\Http\Requests\CreateRapatRequest;
use Modules\Rapat\Http\Requests\UpdateRapatRequest;
use Modules\Rapat\Http\Service\Implementation\RapatService;

class RapatController extends Controller
{
    protected $rapatService;
    public function __construct(RapatService $rapatService)
    {
        $this->rapatService = $rapatService;
    }

    public function index(Request $request)
    {
        $rapat = RapatAgenda::pegawaiIsPesertaOrCreator(Auth::user()->pegawai->id)
            ->when($request->input('agenda_rapat'), function ($query, $agendaRapat) {
                return $query->where('agenda_rapat', 'like', "%{$agendaRapat}%");
            })
            ->when($request->input('dari_tgl'), function ($query, $dari) {
                $query->whereDate('waktu_mulai', '>=', $dari);
            })
            ->when($request->input('sampai_tgl'), function ($query, $sampai) {
                $query->whereDate('waktu_mulai', '<=', $sampai);
            })
            ->when($request->input('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->where(function ($query) {
                $query->where('status', '<>', StatusAgendaRapat::COMPLETED->value)
                    ->orWhereNull('is_penugasan');
            })
            ->orderBy('waktu_mulai', 'asc')
            ->paginate(5)
            ->withQueryString();
        // return $rapat;
        // untuk mengubah status agenda rapat
        $now = Carbon::now('Asia/Jakarta')->toDateTimeString();
        foreach ($rapat as $rapatItem) {
            $tglMulai = Carbon::parse($rapatItem->waktu_mulai)->toDateTimeString();
            if ($now >= $tglMulai && $rapatItem->status == StatusAgendaRapat::SCHEDULED->value) {
                $rapatItem->status = StatusAgendaRapat::STARTED->value;
                $rapatItem->save();
            }
        }
        return view('rapat::rapat.index', [
            'rapats' => $rapat,
        ]);
    }
    public function create()
    {
        $kepanitiaan = Kepanitiaan::pegawaiIsAnggotaPanitia(Auth::user()->pegawai->id)->where('status', 'AKTIF')->get();
        if (! RoleGroupHelper::userHasRoleGroup(Auth::user(), RoleGroupHelper::pimpinanRapatRoles())) {
            $kepanitiaan = $kepanitiaan->where('pimpinan_id', Auth::user()->pegawai->id);
        }
        return view('rapat::rapat.create', [
            'kepanitiaans' => $kepanitiaan,
            'pegawais'     => Pegawai::all(),
        ]);
    }
    public function ajaxPesertaRapat(Request $request)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();
        $query        = Pegawai::with(['user', 'rapatAgendaPeserta'])
            ->withCount(['kepanitiaans as kepanitiaans_aktif_bulan_ini_count' => function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('status', 'AKTIF')
                    ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                        $q->whereDate('tanggal_mulai', '<=', $endOfMonth)
                            ->whereDate('tanggal_berakhir', '>=', $startOfMonth);
                    });
            }]);

        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }
        $total    = Pegawai::whereNotNull('username')->count();
        $filtered = $query->count();

        $data = $query
            ->offset($request->input('start'))
            ->limit($request->input('length'))
            ->get();

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ]);
    }
    public function ajaxSelectedPesertaRapat(Request $request)
    {
        $idPeserta    = explode(',', $request->id);
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();
        $query        = Pegawai::whereIn('id', $idPeserta)
            ->with(['user', 'rapatAgendaPeserta'])
            ->withCount(['kepanitiaans as kepanitiaans_aktif_bulan_ini_count' => function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('status', 'AKTIF')
                    ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                        $q->whereDate('tanggal_mulai', '<=', $endOfMonth)
                            ->whereDate('tanggal_berakhir', '>=', $startOfMonth);
                    });
            }]);

        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }
        $total    = Pegawai::whereNotNull('username')->count();
        $filtered = $query->count();

        $data = $query
            ->offset($request->input('start'))
            ->limit($request->input('length'))
            ->get();

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ]);
    }
    public function store(CreateRapatRequest $request)
    {
        try {
            $validated               = $request->validated();
            $validated['pegawai_id'] = Auth::user()->pegawai->id;
            $this->rapatService->store($validated);
            return response()->json([
                'success' => true,
                'title'   => 'Berhasil',
                'message' => 'Rapat berhasil ditambahkan.',
                'icon'    => 'success',
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal menambahkan Agenda Rapat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal Menambahkan Agenda Rapat',
                'title'   => 'Gagal',
                'icon'    => 'error',
            ]);
        }
    }

    public function show(RapatAgenda $rapatAgenda)
    {
        $rapatAgenda->load(['rapatAgendaPimpinan', 'rapatAgendaNotulis', 'rapatAgendaPeserta', 'rapatLampiran', 'rapatKepanitiaan']);
        return view('rapat::rapat.detail-rapat', [
            'rapat' => $rapatAgenda,
        ]);
    }
    public function downloadLampiran($file)
    {
        try {
            return Storage::download('/public/rapat/' . $file);
        } catch (\Throwable $th) {
            return "File Tidak Ditemukan";
        }
    }
    public function edit(RapatAgenda $rapatAgenda)
    {
        if (
            $rapatAgenda->pimpinan_id !== Auth::user()->pegawai->id &&
            $rapatAgenda->pegawai_id !== Auth::user()->pegawai->id
        ) {
            abort(403);
        }

        $kepanitiaan = Kepanitiaan::where('status', 'AKTIF')->get();
        // $rapatAgenda->load(['rapatAgendaPimpinan', 'rapatKepanitiaan', 'rapatKepanitiaan.pegawai', 'rapatAgendaNotulis', 'rapatAgendaPeserta', 'rapatLampiran']);
        $rapatAgenda->load([
            'rapatAgendaPimpinan'      => function ($query) {
                $query->select('pegawais.id', 'pegawais.nama', 'pegawais.gelar_dpn', 'pegawais.gelar_blk');
            },
            'rapatKepanitiaan',
            'rapatKepanitiaan.pegawai' => function ($query) {
                $query->select('pegawais.id', 'pegawais.nama', 'pegawais.gelar_dpn', 'pegawais.gelar_blk');
            },
            'rapatAgendaNotulis'       => function ($query) {
                $query->select('pegawais.id', 'pegawais.nama', 'pegawais.gelar_dpn', 'pegawais.gelar_blk');
            },
            'rapatAgendaPeserta'       => function ($query) {
                $query->select('pegawais.id', 'pegawais.nama', 'pegawais.gelar_dpn', 'pegawais.gelar_blk');
            },
            'rapatLampiran',
        ]);

        return view('rapat::rapat.edit-rapat', [
            'slug'            => $rapatAgenda->slug,
            'rapatAgenda'     => $rapatAgenda,
            'kepanitiaans'    => $kepanitiaan,
            'pegawais'        => Pegawai::all(),
            'selectedPegawai' => $rapatAgenda->rapatAgendaPeserta->pluck('id'),
        ]);
    }
    public function update(RapatAgenda $rapatAgenda, UpdateRapatRequest $request)
    {
        if (
            $rapatAgenda->pimpinan_id !== Auth::user()->pegawai->id &&
            $rapatAgenda->pegawai_id !== Auth::user()->pegawai->id
        ) {
            abort(403);
        }

        $validated = $request->validated();
        Log::error('data update' . json_encode($validated));
        try {
            $this->rapatService->update($validated, $rapatAgenda);
            return response()->json([
                'success' => true,
                'title'   => 'Berhasil',
                'message' => 'Rapat berhasil Diubah.',
                'icon'    => 'success',
            ]);

        } catch (\Throwable $e) {
            Log::error('Gagal Update Agenda Rapat: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal Mengubah Agenda Rapat',
                'title'   => 'Gagal',
                'icon'    => 'error',
            ]);

        }
    }
    public function ubahStatusRapat(RapatAgenda $rapatAgenda)
    {
        if (
            $rapatAgenda->pimpinan_id !== Auth::user()->pegawai->id &&
            $rapatAgenda->pegawai_id !== Auth::user()->pegawai->id
        ) {
            abort(403);
        }

        try {
            $this->rapatService->ubahStatusAgendaRapat($rapatAgenda);
            FlashMessage::success('Status rapat berhasil diubah');
            return redirect()->to('/rapat/agenda-rapat');
        } catch (\Throwable $th) {
            Log::error('Gagal Mengubah Status Agenda Rapat : ' . $th->getMessage());
            FlashMessage::error('Gagal Mengubah Status Agenda Rapat');
            return redirect()->to('/rapat/agenda-rapat');
        }
    }
    public function formKonfirmasiKesediaanRapat($token)
    {
        $data             = Crypt::decrypt($token);
        $agendaRapat      = RapatAgenda::where('id', $data['rapat_agenda_id'])->first();
        $pegawai          = Pegawai::where('id', $data['pegawai_id'])->first();
        $statusKonfirmasi = '';
        if (! $agendaRapat->rapatAgendaPeserta->contains($pegawai)) {
            abort(403);
        }
        $statusKonfirmasi = optional(
            $pegawai->rapatAgendaPeserta
                ->firstWhere('pivot.rapat_agenda_id', $agendaRapat->id)
        )->pivot->status ?? null;
        return view('rapat::rapat.konfirmasi.konfirmasi-kesediaan-rapat', [
            'rapat'            => $agendaRapat,
            'pegawai'          => $pegawai,
            'statusKonfirmasi' => $statusKonfirmasi,
        ]);
    }
    public function konfirmasiKesediaanRapat(RapatAgenda $rapatAgenda, Pegawai $pegawai, Request $request)
    {
        try {
            $rapatAgenda->rapatAgendaPeserta()->syncWithoutDetaching([
                $pegawai->username => ['status' => StatusPesertaRapat::from($request->status)->value],
            ]);
            return redirect()->back();
        } catch (\Throwable $th) {
            Log::error('Gagal Menambahkan Status Konfirmasi Agenda Rapat : ' . $th->getMessage());
            return redirect()->back();
        }
    }
}
