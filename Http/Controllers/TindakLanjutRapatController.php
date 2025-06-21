<?php
namespace Modules\Rapat\Http\Controllers;

use App\Models\Core\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Rapat\Entities\Pegawai;
use Modules\Rapat\Entities\RapatAgenda;
use Modules\Rapat\Entities\RapatTindakLanjut;
use Modules\Rapat\Http\Helper\FlashMessage;
use Modules\Rapat\Http\Helper\RoleGroupHelper;
use Modules\Rapat\Http\Requests\CreateTugasPesertaRapatRequest;
use Modules\Rapat\Http\Requests\UploadTugasTindakLanjutRapatRequest;
use Modules\Rapat\Http\Service\Implementation\TindakLanjutRapatService;
use Modules\Rapat\Rules\EnumKriteriaPenilaianRule;

class TindakLanjutRapatController extends Controller
{
    private $tindakLanjutRapatService;
    public function __construct(TindakLanjutRapatService $tindakLanjutRapatService)
    {
        $this->tindakLanjutRapatService = $tindakLanjutRapatService;
    }
    public function index(Request $request)
    {
        $tindakLanjutRapat = '';
        if (RoleGroupHelper::userHasRoleGroup(Auth::user(), RoleGroupHelper::pimpinanRoles())) {
            $tindakLanjutRapat = RapatTindakLanjut::with('rapatAgenda');
        } else {
            $tindakLanjutRapat = RapatTindakLanjut::listAgendaRapatHaveTugas(Auth::user()->pegawai->username)->with('rapatAgenda');
        }

        // Langkah 1: Ambil satu ID dari setiap grup rapat_agenda_id
        // Di sini kita pakai MIN(id) agar mengambil data pertama/tertua dari masing-masing rapat_agenda_id
        $distinctIds = $tindakLanjutRapat->selectRaw('MIN(id) as id')
        // Filter jika ada input pencarian berdasarkan nama agenda rapat
            ->when($request->input('cari'), function ($query, $cari) {
                $query->whereHas('rapatAgenda', function ($q) use ($cari) {
                    $q->where('agenda_rapat', 'like', "%$cari%");
                });
            })
        // Filter berdasarkan tanggal mulai dari input 'dari_tgl'
            ->when($request->input('dari_tgl'), function ($query, $dari) {
                $query->whereHas('rapatAgenda', function ($q) use ($dari) {
                    $q->whereDate('waktu_mulai', '>=', $dari);
                });
            })
        // Filter berdasarkan tanggal sampai dari input 'sampai_tgl'
            ->when($request->input('sampai_tgl'), function ($query, $sampai) {
                $query->whereHas('rapatAgenda', function ($q) use ($sampai) {
                    $q->whereDate('waktu_mulai', '<=', $sampai);
                });
            })
            ->when($request->input('status'), function ($query, $status) {
                $query->where('status', $status);
            })
        // Group by rapat_agenda_id agar hanya ambil 1 ID dari tiap agenda
            ->groupBy('rapat_agenda_id')
        // Ambil hasilnya sebagai array ID
            ->pluck('id');
        // Langkah 2: Ambil data lengkap dari RapatTindakLanjut berdasarkan ID yang telah dipilih di atas
        $tindakLanjutRapat = RapatTindakLanjut::with('rapatAgenda.rapatTindakLanjut')
            ->whereIn('id', $distinctIds) // hanya ambil data yang ID-nya sesuai dengan hasil langkah 1
            ->orderBy('created_at', 'asc')
            ->paginate(10);
        // return $tindakLanjutRapat;
        return view('rapat::rapat.tindak-lanjut.index', [
            'tindakLanjutRapat' => $tindakLanjutRapat,
        ]);
    }
    public function show(RapatAgenda $rapatAgenda)
    {
        $tindakLanjut = '';
        if (RoleGroupHelper::userHasRoleGroup(Auth::user(), RoleGroupHelper::pimpinanRoles())) {
            $tindakLanjut = $rapatAgenda->rapatTindakLanjut()->with(['rapatTindakLanjutFile', 'rapatAgenda', 'pegawai'])->get();
        } else {
            $tindakLanjut = $rapatAgenda->rapatTindakLanjut()->pegawaiHaveTugas(Auth::user()->pegawai, $rapatAgenda)->with(['rapatTindakLanjutFile', 'rapatAgenda', 'pegawai'])->get();
        }
        return view('rapat::rapat.tindak-lanjut.lihat-tindak-lanjut', [
            'rapat'         => $rapatAgenda,
            'tindakLanjuts' => $tindakLanjut,
        ]);
    }
    public function isiPenugasan(RapatAgenda $rapatAgenda)
    {
        $rapatAgenda->load(['rapatAgendaPimpinan', 'rapatAgendaNotulis', 'rapatAgendaPeserta']);
        $data         = [];
        $btnPenugasan = '';
        foreach ($rapatAgenda->rapatAgendaPeserta as $key => $peserta) {
            if ($peserta->username == $rapatAgenda->notulis_username) {
                continue;
            }
            if ($peserta->username == $rapatAgenda->pimpinan_username) {
                continue;
            }
            if ($peserta->pivot->is_penugasan == false) {
                $btnPenugasan = '<a href="' . url('/rapat/agenda-rapat/' . $rapatAgenda->slug . '/tugaskan/' . $peserta->username) . '" class="btn btn-primary">Tugaskan</a>';
            } else {
                $btnPenugasan = ' <button class="btn btn-danger">Sudah Ditugaskan</button>';
            }
            $data[] = [
                $key + 1,
                $peserta->formatted_name,
                $btnPenugasan,
            ];
        }
        return view('rapat::rapat.tindak-lanjut.input-penugasan', [
            'rapat' => $rapatAgenda,
            'data'  => $data,
        ]);
    }
    public function tugaskanPesertaRapat(RapatAgenda $rapatAgenda, Pegawai $pegawai)
    {
        $this->isUserArePesertaRapat($rapatAgenda, $pegawai);
        return view('rapat::rapat.tindak-lanjut.tugaskan', [
            'rapat'   => $rapatAgenda,
            'peserta' => $pegawai,
        ]);
    }
    public function createTugasPesertaRapat(RapatAgenda $rapatAgenda, Pegawai $pegawai, CreateTugasPesertaRapatRequest $request)
    {
        $this->isUserArePesertaRapat($rapatAgenda, $pegawai);
        $validated = $request->validated();
        try {
            $this->tindakLanjutRapatService->createTugasPesertaRapat($rapatAgenda, $pegawai, $validated);
            FlashMessage::success('Tugas Berhasil Ditambahkan');
            return redirect()->to('/rapat/agenda-rapat/' . $rapatAgenda->slug . '/tugas');
        } catch (\Throwable $e) {
            FlashMessage::error("Gagal Menambahkan Tugas");
            return redirect()->to('/rapat/agenda-rapat/' . $rapatAgenda->slug . '/tugas');
        }
    }

    public function showUploadTugas(RapatTindakLanjut $rapatTindakLanjut)
    {
        if (Auth::user()->username != $rapatTindakLanjut->pegawai->username) {
            abort(403);
        }
        return view('rapat::rapat.tindak-lanjut.upload-tugas', [
            'rapatTindakLanjut' => $rapatTindakLanjut,
        ]);
    }
    public function uploadTugas(RapatTindakLanjut $rapatTindakLanjut, UploadTugasTindakLanjutRapatRequest $request)
    {
        $validated = $request->validated();
        try {
            $this->tindakLanjutRapatService->uploadTugas($rapatTindakLanjut, $validated);
            FlashMessage::success('Tugas Berhasil Di Unggah');
            return redirect()->to('/rapat/tindak-lanjut-rapat/' . $rapatTindakLanjut->rapatAgenda->slug . '/detail');
        } catch (\Throwable $e) {
            FlashMessage::error("Gagal Upload Tugas");
            return redirect()->to('/rapat/tindak-lanjut-rapat/' . $rapatTindakLanjut->rapatAgenda->slug . '/detail');
        }
    }
    public function showEditTugas(RapatTindakLanjut $rapatTindakLanjut)
    {
        return view('rapat::rapat.tindak-lanjut.ubah-tugas', [
            'rapatTindakLanjut' => $rapatTindakLanjut,
        ]);
    }
    public function editTugas(RapatTindakLanjut $rapatTindakLanjut, UploadTugasTindakLanjutRapatRequest $request)
    {
        $validated = $request->validated();
        try {
            $this->tindakLanjutRapatService->editTugas($rapatTindakLanjut, $validated);
            FlashMessage::success('Tugas Berhasil Di Edit');
            return redirect()->to('/rapat/tindak-lanjut-rapat/' . $rapatTindakLanjut->rapatAgenda->slug . '/detail');
        } catch (\Throwable $e) {
            FlashMessage::error("Gagal Edit Tugas");
            return redirect()->to('/rapat/tindak-lanjut-rapat/' . $rapatTindakLanjut->rapatAgenda->slug . '/detail');
        }
    }
    public function detailTugas(RapatTindakLanjut $rapatTindakLanjut)
    {
        $rapatTindakLanjut->load(['rapatTindakLanjutFile', 'rapatAgenda']);
        return view('rapat::rapat.tindak-lanjut.detail-tugas', [
            'tindakLanjut' => $rapatTindakLanjut,
        ]);
    }
    public function simpanTugas(RapatTindakLanjut $rapatTindakLanjut, Request $request)
    {
        if (Auth::user()->username != $rapatTindakLanjut->rapatAgenda->rapatAgendaPimpinan->username) {
            abort(403);
        }
        $validated = $request->validate([
            'kriteria_penilaian' => ['required', new EnumKriteriaPenilaianRule],
            'komentar_penugasan' => 'nullable',
        ]);
        try {
            $this->tindakLanjutRapatService->simpanTugas($validated, $rapatTindakLanjut);
            FlashMessage::success('Penilaian Berhasil Di Simpan');
            return redirect()->to('/rapat/tindak-lanjut-rapat/' . $rapatTindakLanjut->rapatAgenda->slug . '/detail');
        } catch (\Throwable $e) {
            FlashMessage::error("Penilaian Gagal Di Simpan");
            return redirect()->to('/rapat/tindak-lanjut-rapat/' . $rapatTindakLanjut->rapatAgenda->slug . '/detail');
        }
    }
    public function tidakAdaTugas(RapatAgenda $rapatAgenda)
    {
        try {
            $rapatAgenda->update(['is_penugasan' => false]);
            FlashMessage::error("Gagal Mengubah Status Penugasan");
            return redirect()->to('/rapat/tindak-lanjut-rapat/');
        } catch (\Throwable $e) {
            return redirect()->back();
        }
    }
    // untuk cek apakah user adalah peserta
    public function isUserArePesertaRapat(RapatAgenda $rapatAgenda, Pegawai $user)
    {
        if (! $rapatAgenda->rapatAgendaPeserta->contains($user)) {
            abort(404);
        }
        return;
    }
}
