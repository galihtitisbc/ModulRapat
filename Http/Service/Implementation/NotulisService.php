<?php
namespace Modules\Rapat\Http\Service\Implementation;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Rapat\Http\Helper\StatusAgendaRapat;
use Modules\Rapat\Http\Helper\StatusPesertaRapat;

class NotulisService
{
    public function storeNotulen($rapatAgenda, $data)
    {
        try {
            DB::beginTransaction();
            $notulen = $rapatAgenda->rapatNotulen()->create([
                'catatan' => $data['catatan_rapat'],
            ]);
            $namafileNotulen     = [];
            $namafileDokumentasi = [];
            //upload notulen file
            if (isset($data['notulen_file'])) {
                $fileNotulen = [];
                foreach ($data['notulen_file'] as $index => $fileNotulen) {
                    $originalName = $fileNotulen->getClientOriginalName();
                    $extension    = $fileNotulen->getClientOriginalExtension();
                    $baseName     = pathinfo($originalName, PATHINFO_FILENAME);
                    $timestamp    = time();
                    $suffix       = "{$timestamp}_{$index}";
                    $fileName     = "{$baseName}_{$suffix}.{$extension}";

                    Storage::putFileAs('public/notulen', $fileNotulen, $fileName);
                    $namafileNotulen[] = [
                        'nama_file' => $fileName,
                    ];
                }
            }
            //upload dokumentasi rapat
            if (isset($data['dokumentasi_file'])) {
                $fileNotulen = [];
                foreach ($data['dokumentasi_file'] as $index => $fileDokumentasi) {
                    $fileName = time() . "_{$index}_" . $fileDokumentasi->getClientOriginalName();
                    Storage::putFileAs('public/dokumentasi-rapat', $fileDokumentasi, $fileName);
                    $namafileDokumentasi[] = [
                        'foto' => $fileName,
                    ];
                }
            }
            //ganti status peserta rapat
            $pesertaUsername = [];
            foreach ($data['peserta_hadir'] as $peserta) {
                $pesertaUsername[$peserta] = [
                    'status' => StatusPesertaRapat::HADIR->value,
                ];
            }
            //peserta yang tidak hadir
            $pesertaTidakHadirUsername = [];
            $pesertaTidakHadir         = $rapatAgenda->rapatAgendaPeserta()->whereNotIn('pegawai_id', $data['peserta_hadir'])->get();
            foreach ($pesertaTidakHadir as $peserta) {
                $pesertaTidakHadirUsername[$peserta->username] = [
                    'status' => StatusPesertaRapat::TIDAK_HADIR->value,
                ];
            }
            //update status peserta rapat yang hadir
            $rapatAgenda->rapatAgendaPeserta()->syncWithoutDetaching($pesertaUsername);
            //update status peserta rapat yang tidak hadir
            $rapatAgenda->rapatAgendaPeserta()->syncWithoutDetaching($pesertaTidakHadirUsername);

            $notulen->notulenFiles()->createMany($namafileNotulen);
            $rapatAgenda->rapatDokumentasi()->createMany($namafileDokumentasi);
            $rapatAgenda->update([
                'status' => StatusAgendaRapat::COMPLETED->value,
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
