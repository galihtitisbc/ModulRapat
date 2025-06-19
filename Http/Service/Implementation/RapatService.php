<?php
namespace Modules\Rapat\Http\Service\Implementation;

use App\Models\Core\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Rapat\Entities\RapatAgenda;
use Modules\Rapat\Http\Helper\StatusAgendaRapat;
use Modules\Rapat\Jobs\CreateMeetingZoom;
use Modules\Rapat\Jobs\WhatsappSender;
use Spatie\Permission\Models\Permission;

class RapatService
{
    public function store(array $data)
    {
        try {
            DB::beginTransaction();
            $googleCalendarLink = $this->generateGoogleCalendarLink($data);
            $agendaRapat        = RapatAgenda::create([
                'pegawai_username'  => $data['pegawai_username'],
                'pimpinan_username' => $data['pimpinan_username'],
                'notulis_username'  => $data['notulis_username'],
                'kepanitiaan_id'    => $data['kepanitiaan_id'] == "" ? null : $data['kepanitiaan_id'],
                'nomor_surat'       => $data['nomor_surat'],
                'waktu_mulai'       => $data['waktu_mulai'],
                'waktu_selesai'     => $data['waktu_selesai'] == "SELESAI" ? null : $data['waktu_selesai'],
                'agenda_rapat'      => $data['agenda_rapat'],
                'tempat'            => $data['tempat'],
                'status'            => 'SCHEDULED',
                'calendar_link'     => $googleCalendarLink,
            ]);
            if (isset($data['lampiran'])) {
                //simpan lampiran ke storage
                $namaLampiran = [];
                foreach ($data['lampiran'] as $index => $lampiran) {
                    $originalName = $lampiran->getClientOriginalName();
                    $extension    = $lampiran->getClientOriginalExtension();
                    $baseName     = pathinfo($originalName, PATHINFO_FILENAME);
                    $timestamp    = time();
                    $suffix       = "{$timestamp}_{$index}";
                    $fileName     = "{$baseName}_{$suffix}.{$extension}";

                    Storage::putFileAs('public/rapat', $lampiran, $fileName);
                    $namaLampiran[] = [
                        'nama_file' => $fileName,
                    ];
                }
                $agendaRapat->rapatLampiran()->createMany($namaLampiran);
            }
            //asosiasikan peserta rapat berserta link konfirmasi kehadiran
            $pivotData = [];
            foreach ($data['peserta_rapat'] as $pesertaId) {
                $payload = [
                    'username'        => $pesertaId,
                    'rapat_agenda_id' => $agendaRapat->id,
                ];
                $linkKonfirmasi        = $this->createKesediaanRapatLink($payload);
                $pivotData[$pesertaId] = [
                    'link_konfirmasi' => $linkKonfirmasi,
                ];
            }
            $agendaRapat->rapatAgendaPeserta()->attach($pivotData);
            //berikan permission ke pimpinan rapat untuk melakukan update dan pembatalan rapat
            $this->tambahkanPermissionKepadaPimpinanRapat($agendaRapat->rapatAgendaPimpinan->user);
            if ($data['tempat'] == 'zoom') {
                CreateMeetingZoom::dispatch($agendaRapat)->chain([
                    new WhatsappSender($agendaRapat, 'rapat', 'tambahRapat'),
                ]);
            } else {
                WhatsappSender::dispatch($agendaRapat, 'rapat', 'tambahRapat');
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new Exception("Gagal Membuat Agenda Rapat : " . $e->getMessage());
        }
    }
    public function ubahStatusAgendaRapat(RapatAgenda $agendaRapat)
    {
        try {
            $now = Carbon::now('Asia/Jakarta');
            if (Carbon::parse($agendaRapat->waktu_mulai)->lessThan($now)) {
                throw new Exception("Waktu Mulai Rapat sudah Terlewat, silahkan ubah waktu mulai rapat terlebih dahulu");
            }
            if (StatusAgendaRapat::SCHEDULED->value == $agendaRapat->status) {
                $agendaRapat->status = StatusAgendaRapat::CANCELLED->value;
                $agendaRapat->save();
                WhatsappSender::dispatch($agendaRapat, 'rapat', 'batalRapat');
            } else {
                $agendaRapat->status = StatusAgendaRapat::SCHEDULED->value;
                $agendaRapat->save();
                WhatsappSender::dispatch($agendaRapat, 'rapat', 'jadwalUlangRapat');
            }
        } catch (\Throwable $th) {
            throw new Exception("Gagal Mengubah Status Agenda Rapat : " . $th->getMessage());
        }
    }
    public function update(array $data, $agendaRapat)
    {
        try {
            $oldTempat = $agendaRapat->tempat;
            DB::beginTransaction();
            $agendaRapat->update([
                'pimpinan_username' => $data['pimpinan_username'],
                'notulis_username'  => $data['notulis_username'],
                'kepanitiaan_id'    => $data['kepanitiaan_id'] == "" ? null : $data['kepanitiaan_id'],
                'nomor_surat'       => $data['nomor_surat'],
                'waktu_mulai'       => $data['waktu_mulai'],
                'waktu_selesai'     => $data['waktu_selesai'] == "SELESAI" ? null : $data['waktu_selesai'],
                'agenda_rapat'      => $data['agenda_rapat'],
                'tempat'            => $data['tempat'],
            ]);
            if (isset($data['lampiran'])) {
                //hapus lampiran lama
                if ($agendaRapat->rapatLampiran->isNotEmpty()) {
                    foreach ($agendaRapat->rapatLampiran as $lampiran) {
                        Storage::delete('public/rapat/' . $lampiran->nama_file);
                    }
                    $agendaRapat->rapatLampiran()->delete();
                }
                //upload lampiran
                $namaLampiran = [];
                foreach ($data['lampiran'] as $index => $lampiran) {
                    $originalName = $lampiran->getClientOriginalName();
                    $extension    = $lampiran->getClientOriginalExtension();
                    $baseName     = pathinfo($originalName, PATHINFO_FILENAME);

                    $timestamp = time();
                    $suffix    = "{$timestamp}_{$index}";
                    $fileName  = "{$baseName}_{$suffix}.{$extension}";

                    Storage::putFileAs('public/rapat', $lampiran, $fileName);
                    $namaLampiran[] = [
                        'nama_file' => $fileName,
                    ];
                }
                $agendaRapat->rapatLampiran()->createMany($namaLampiran);
            }
            //asosiasikan peserta rapat berserta link konfirmasi kehadiran
            $pivotData = [];
            foreach ($data['peserta_rapat'] as $pesertaId) {
                $payload = [
                    'username'        => $pesertaId,
                    'rapat_agenda_id' => $agendaRapat->id,
                ];
                $linkKonfirmasi        = $this->createKesediaanRapatLink($payload);
                $pivotData[$pesertaId] = [
                    'link_konfirmasi' => $linkKonfirmasi,
                ];
            }
            $agendaRapat->rapatAgendaPeserta()->sync($pivotData);
            //berikan permission ke pimpinan rapat untuk melakukan update dan pembatalan rapat
            $this->tambahkanPermissionKepadaPimpinanRapat($agendaRapat->rapatAgendaPimpinan->user);

            //jika tempat rapat diubah dari tempat lain ke zoom
            if ($oldTempat != 'zoom' && $data['tempat'] == 'zoom') {
                CreateMeetingZoom::dispatch($agendaRapat)->chain([
                    new WhatsappSender($agendaRapat, 'rapat', 'updateRapat'),
                ]);
            } else {
                WhatsappSender::dispatch($agendaRapat, 'rapat', 'updateRapat');
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new Exception("Gagal Mengubah Agenda Rapat : " . $th->getMessage());
        }
    }

    private function tambahkanPermissionKepadaPimpinanRapat(User $user)
    {
        $kecuali     = ['rapat.agenda.create', 'rapat.agenda.store'];
        $permissions = Permission::where('name', 'like', '%' . 'rapat.agenda' . '%')->whereNotIn('name', $kecuali)->get();
        $user->givePermissionTo($permissions);
    }
    function generateGoogleCalendarLink(array $data)
    {
        $title    = urlencode($data['agenda_rapat']);
        $location = urlencode($data['tempat']);
        $details  = urlencode('Agenda Rapat: ' . $data['agenda_rapat']);

        $start = Carbon::parse($data['waktu_mulai']);
        $end   = '';
        if (strtotime($data['waktu_selesai'])) {
            $end = Carbon::parse($data['waktu_selesai']);
        } else {
            $end = $start->copy()->addHour();
        }

        $startFormatted = $start->format('Ymd\THis');
        $endFormatted   = $end->format('Ymd\THis');

        $url = "https://calendar.google.com/calendar/u/0/r/eventedit?" .
            "text={$title}" .
            "&dates={$startFormatted}/{$endFormatted}" .
            "&details={$details}" .
            "&location={$location}" .
            "&ctz=Asia/Jakarta";

        return $url;
    }
    public function createKesediaanRapatLink($data)
    {
        $token = Crypt::encrypt($data);
        return env('APP_URL') . '/rapat/agenda-rapat/konfirmasi/' . $token;
    }
}
