<?php
namespace Modules\Rapat\Http\Controllers;

use App\Models\Core\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Rapat\Entities\Kepanitiaan;
use Modules\Rapat\Entities\Pegawai;
use Modules\Rapat\Http\Helper\FlashMessage;
use Modules\Rapat\Http\Helper\RoleGroupHelper;
use Modules\Rapat\Http\Requests\KepanitiaanRequest;
use Modules\Rapat\Http\Requests\UpdateKepanitiaanRequest;
use Modules\Rapat\Jobs\WhatsappSenderKepanitiaan;

class KepegawaianController extends Controller
{
    public function index(Request $request)
    {
        $kepanitiaans = '';

        if (RoleGroupHelper::userHasRoleGroup(Auth::user(), RoleGroupHelper::kepegawaianRoles()) || RoleGroupHelper::userHasRoleGroup(Auth::user(), RoleGroupHelper::pimpinanRoles())) {
            $kepanitiaans = Kepanitiaan::with('pegawai');
        } else {
            $kepanitiaans = Kepanitiaan::pegawaiIsAnggotaPanitia(Auth::user()->pegawai->username)->with('pegawai');
        }
        $kepanitiaans = $kepanitiaans
            ->when($request->input('nama_kepanitiaan'), function ($query, $namaKepanitiaan) {
                $query->where('nama_kepanitiaan', 'like', '%' . $namaKepanitiaan . '%');
            })
            ->when($request->input('dari_tgl'), function ($query, $dari) {
                $query->whereDate('tanggal_mulai', '>=', $dari);
            })
            ->when($request->input('sampai_tgl'), function ($query, $sampai) {
                $query->whereDate('tanggal_berakhir', '<=', $sampai);
            })
            ->when($request->input('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
        return view('rapat::kepegawaian.index', [
            'kepanitiaans' => $kepanitiaans,
        ]);
    }
    public function detail(Kepanitiaan $kepanitiaan)
    {
        $kepanitiaan->load('ketua');
        $strukturKepanitiaan = json_decode($kepanitiaan->struktur, true);
        $usernames           = array_column($strukturKepanitiaan, 'username'); //ambil data username, untuk dicari di table pegawai, karena butuh data dari table pegawai
        $pegawai             = Pegawai::whereIn('username', $usernames)->get();

        $dataStruktur = [];
        foreach ($strukturKepanitiaan as $value) {
            if ($pegawaiModel = $pegawai->where('username', $value['username'])->first()) {
                $value['pegawai'] = $pegawaiModel;
            } else {
                $value['pegawai'] = null;
            }
            $dataStruktur[] = $value;
        }
        return view('rapat::kepegawaian.detail', [
            'panitia'  => $kepanitiaan,
            'struktur' => $dataStruktur,
        ]);
    }
    public function ajaxKepanitiaanRapat($id)
    {
        try {
            $kepanitiaan = Kepanitiaan::with('pegawai')->where('id', $id)->firstOrFail();
            return response()->json($kepanitiaan);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Kepanitiaan Tidak Ditemukan',
            ], 404);
        }
    }
    public function create()
    {
        $pegawais = Pegawai::all();
        return view('rapat::kepegawaian.create', [
            'pegawais' => $pegawais,
        ]);
    }

    public function store(KepanitiaanRequest $request)
    {
        try {
            $validated             = $request->validated();
            $validated['struktur'] = json_encode($validated['struktur_kepanitiaan']);
            $kepanitiaan           = Kepanitiaan::create($validated);
            $kepanitiaan->pegawai()->attach($validated['peserta_panitia']);
            WhatsappSenderKepanitiaan::dispatch($kepanitiaan, 'create');
            return response()->json(['message' => 'Kepanitiaan berhasil ditambahkan.']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()]);
        }
    }

    public function edit(Kepanitiaan $kepanitiaan)
    {
        $kepanitiaan->load('pegawai');
        $selectedPegawai = $kepanitiaan->pegawai->pluck('username')->toArray();
        return view('rapat::kepegawaian.edit', [
            'kepanitiaan'     => $kepanitiaan,
            'selectedPegawai' => $selectedPegawai,
        ]);
    }

    public function update(UpdateKepanitiaanRequest $request, Kepanitiaan $kepanitiaan)
    {
        try {
            $validated             = $request->validated();
            $validated             = $request->validated();
            $validated['struktur'] = json_encode($validated['struktur_kepanitiaan']);
            $kepanitiaan->update($validated);
            $kepanitiaan->pegawai()->sync($validated['peserta_panitia']);
            WhatsappSenderKepanitiaan::dispatch($kepanitiaan, 'update');
            return response()->json(['message' => 'Kepanitiaan berhasil diubah.']);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Gagal Mengubah kepanitiaan.']);
        }
    }

    public function changeStatus(Kepanitiaan $kepanitiaan)
    {
        try {
            $kepanitiaan->update([
                'status' => $kepanitiaan->status == 'AKTIF' ? 'NON_AKTIF' : 'AKTIF',
            ]);
            FlashMessage::success('Status Kepanitiaan Berhasil Di Diubah');
            return redirect()->to('/rapat/panitia');
        } catch (\Throwable $th) {
            FlashMessage::error('Status Kepanitiaan Gagal Di Ubah');
            return redirect()->to('/rapat/panitia');
        }
    }
    public function download(Kepanitiaan $kepanitiaan)
    {
        $kepanitiaan->load(['pegawai', 'ketua']);
        $strukturKepanitiaan = json_decode($kepanitiaan->struktur, true);
        $usernames           = array_column($strukturKepanitiaan, 'username'); //ambil data username, untuk dicari di table pegawai, karena butuh data dari table pegawai
        $pegawai             = Pegawai::whereIn('username', $usernames)->get();

        $dataStruktur = [];
        foreach ($strukturKepanitiaan as $value) {
            if ($pegawaiModel = $pegawai->where('username', $value['username'])->first()) {
                $value['pegawai'] = $pegawaiModel;
            } else {
                $value['pegawai'] = null;
            }
            $dataStruktur[] = $value;
        }

        return view('rapat::kepegawaian.surat_tugas_pdf', [
            'kepanitiaan' => $kepanitiaan,
            'struktur'    => $dataStruktur,
        ]);
    }
}
