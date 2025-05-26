<?php
namespace Modules\Rapat\Http\Service\Implementation;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Rapat\Entities\Pegawai;
use Modules\Rapat\Entities\RapatAgenda;
use Modules\Rapat\Entities\RapatTindakLanjut;
use Modules\Rapat\Http\Helper\StatusTindakLanjut;
use Modules\Rapat\Jobs\WhatsappSender;

class TindakLanjutRapatService
{
    public function createTugasPesertaRapat(RapatAgenda $rapatAgenda, Pegawai $pegawai, array $data)
    {
        try {
            DB::beginTransaction();
            $tindakLanjut = $rapatAgenda->rapatTindakLanjut()->create([
                'pegawai_username' => $pegawai->username,
                'deskripsi_tugas'  => $data['deskripsi'],
                'batas_waktu'      => $data['batas_waktu'],
            ]);
            $rapatAgenda->rapatAgendaPeserta()->syncWithoutDetaching([
                $pegawai->username => ['is_penugasan' => 1],
            ]);
            WhatsappSender::dispatch(
                agendaRapat: $rapatAgenda,
                type: 'penugasan',
                status: 'penugasan',
                tindakLanjut: $tindakLanjut
            );
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    public function uploadTugas($tindakLanjutRapat, $data)
    {
        try {
            DB::beginTransaction();
            if (isset($data['file_tugas'])) {
                $fileTugas = [];
                foreach ($data['file_tugas'] as $index => $fileTugas) {
                    $fileName = time() . "_{$index}_" . $fileTugas->getClientOriginalName();
                    Storage::putFileAs('public/tindakLanjut', $fileTugas, $fileName);
                    $namafileTugas[] = [
                        'nama_file' => $fileName,
                    ];
                }
                $tindakLanjutRapat->rapatTindakLanjutFile()->createMany($namafileTugas);
            }
            $tindakLanjutRapat->update([
                'status'          => StatusTindakLanjut::SELESAI->value,
                'tugas'           => $data['tugas'],
                'kendala'         => $data['kendala'],
                'tanggal_selesai' => now(),
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
        }
    }
    public function editTugas($tindakLanjutRapat, $data)
    {
        try {
            DB::beginTransaction();
            if (isset($data['file_tugas'])) {
                if ($tindakLanjutRapat->rapatTindakLanjutFile->isNotEmpty()) {
                    foreach ($tindakLanjutRapat->rapatTindakLanjutFile as $file) {
                        Storage::delete('public/tindakLanjut/' . $file->nama_file);
                    }
                    $tindakLanjutRapat->rapatTindakLanjutFile()->delete();
                }
                $fileTugas = [];
                foreach ($data['file_tugas'] as $index => $fileTugas) {
                    $fileName = time() . "_{$index}_" . $fileTugas->getClientOriginalName();
                    Storage::putFileAs('public/tindakLanjut', $fileTugas, $fileName);
                    $namafileTugas[] = [
                        'nama_file' => $fileName,
                    ];
                }
                $tindakLanjutRapat->rapatTindakLanjutFile()->createMany($namafileTugas);
            }
            $tindakLanjutRapat->update([
                'status'          => StatusTindakLanjut::SELESAI->value,
                'tugas'           => $data['tugas'],
                'kendala'         => $data['kendala'],
                'tanggal_selesai' => now(),
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
        }
    }
    public function simpanTugas($data, $tindakLanjut)
    {
        try {
            DB::beginTransaction();
            $tindakLanjut->update([
                'penilaian' => $data['kriteria_penilaian'],
                'komentar'  => $data['komentar_penugasan'],
            ]);
            WhatsappSender::dispatch(
                agendaRapat: $tindakLanjut->rapatAgenda,
                type: 'penilaian',
                status: 'penilaian',
                tindakLanjut: $tindakLanjut
            );

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
        }
    }
}
