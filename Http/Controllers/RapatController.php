<?php
namespace Modules\Rapat\Http\Controllers;

use App\Models\Core\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Modules\Rapat\Entities\Kepanitiaan;
use Modules\Rapat\Entities\Pegawai;
use Modules\Rapat\Entities\RapatAgenda;
use Modules\Rapat\Http\Helper\FlashMessage;
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
        $rapat = RapatAgenda::pegawaiIsPesertaOrCreator(Auth::user()->pegawai->username)
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
            ->orderBy('waktu_mulai', 'asc')
            ->paginate(5)
            ->withQueryString();
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
        $kepanitiaan = Kepanitiaan::where('status', 'AKTIF')->get();
        return view('rapat::rapat.create', [
            'kepanitiaans' => $kepanitiaan,
        ]);
    }
    public function ajaxPesertaRapat(Request $request)
    {
        $query = Pegawai::with(['user', 'rapatAgendaPeserta', 'kepanitiaans'])->whereNotNull('username');
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }
        $total    = Pegawai::count();
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
        $usernamePeserta = explode(',', $request->username);
        $query           = Pegawai::whereIn('username', $usernamePeserta)->with(['user', 'rapatAgendaPeserta', 'kepanitiaans']);
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('user.email', 'like', "%{$search}%");
            });
        }
        $total    = Pegawai::count();
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
            $validated                     = $request->validated();
            $validated['pegawai_username'] = Auth::user()->pegawai->username;
            $this->rapatService->store($validated);
            return response()->json([
                'success' => true,
                'title'   => 'Berhasil',
                'message' => 'Rapat berhasil ditambahkan.',
                'icon'    => 'success',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'title'   => 'Gagal',
                'icon'    => 'error',
            ]);
        }
    }

    public function show(RapatAgenda $rapatAgenda)
    {
        $rapatAgenda->load(['rapatAgendaPimpinan', 'rapatAgendaNotulis', 'rapatAgendaPeserta', 'rapatLampiran', 'rapatAgendaPeserta.user']);
        return view('rapat::rapat.detail-rapat', [
            'rapat' => $rapatAgenda,
        ]);
    }
    public function downloadLampiran($file)
    {
        return Storage::download('/public/rapat/' . $file);
    }
    public function edit(RapatAgenda $rapatAgenda)
    {
        $kepanitiaan = Kepanitiaan::where('status', 'AKTIF')->get();
        $rapatAgenda->load(['rapatAgendaPimpinan', 'rapatKepanitiaan', 'rapatKepanitiaan.pegawai', 'rapatAgendaNotulis', 'rapatAgendaPeserta', 'rapatLampiran']);
        return view('rapat::rapat.edit-rapat', [
            'slug'         => $rapatAgenda->slug,
            'rapatAgenda'  => $rapatAgenda,
            'kepanitiaans' => $kepanitiaan,
        ]);
    }
    public function ajaxEditRapat(RapatAgenda $rapatAgenda)
    {
        $rapatAgenda->load(['rapatAgendaPimpinan', 'rapatKepanitiaan', 'rapatKepanitiaan.pegawai', 'rapatAgendaNotulis', 'rapatAgendaPeserta', 'rapatLampiran']);
        return response()->json($rapatAgenda);
    }
    public function update(RapatAgenda $rapatAgenda, UpdateRapatRequest $request)
    {
        $validated = $request->validated();
        try {
            $this->rapatService->update($validated, $rapatAgenda);
            return response()->json([
                'success' => true,
                'title'   => 'Berhasil',
                'message' => 'Rapat berhasil Diubah.',
                'icon'    => 'success',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'title'   => 'Gagal',
                'icon'    => 'error',
            ]);

        }
    }
    public function ubahStatusRapat(RapatAgenda $rapatAgenda)
    {
        try {
            $this->rapatService->ubahStatusAgendaRapat($rapatAgenda);
            FlashMessage::success('Status rapat berhasil diubah');
            return redirect()->to('/rapat/agenda-rapat');
        } catch (\Throwable $th) {
            FlashMessage::error($th->getMessage());
            return redirect()->to('/rapat/agenda-rapat');
        }
    }
    public function formKonfirmasiKesediaanRapat($token)
    {
        $data             = Crypt::decrypt($token);
        $agendaRapat      = RapatAgenda::where('id', $data['rapat_agenda_id'])->first();
        $pegawai          = Pegawai::where('username', $data['username'])->first();
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
            return redirect()->back();
        }
    }
}
