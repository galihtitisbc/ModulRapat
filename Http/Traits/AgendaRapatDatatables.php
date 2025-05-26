<?php
namespace Modules\Rapat\Http\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Rapat\Http\Helper\StatusAgendaRapat;

trait AgendaRapatDatatables
{
    public function getAgendaRapatDatatables($rapats)
    {
        Carbon::setLocale('id');
        $statusRapat = [
            'CANCELED'  => ['danger', 'Di Batalkan'],
            'SCHEDULED' => ['warning', 'Di Jadwalkan'],
            'COMPLETED' => ['success', 'Selesai'],
            'STARTED'   => ['primary', 'Sedang Berlangsung'],
        ];
        $statusKeaktifan = [
            'SCHEDULED' => ['fa-calendar-times', '#ff0000'],
            'CANCELED'  => ['fa-undo', '#5cb85c'],
            'COMPLETED' => ['fas fa-check-circle', '#28a745'],
            'STARTED'   => ['fas fa-play-circle', '#0275d8'],
        ];
        $data            = [];
        $showTugasColumn = collect($rapats)->contains(function ($rapat) {
            return $rapat->pimpinan_username === Auth::user()->pegawai->username || $rapat->notulis_username === Auth::user()->pegawai->username;
        });
        foreach ($rapats as $index => $rapat) {
            $startTime   = Carbon::parse($rapat->waktu_mulai)->translatedFormat('l, d F Y H:i');
            $statusBadge = '<span class="badge bg-' . $statusRapat[$rapat->status][0] . '">' . $statusRapat[$rapat->status][1] . '</span>';

            $aksi = '<a href="' . url('rapat/agenda-rapat/' . $rapat->slug . '/detail') . '">
                    <i class="fas fa-eye fa-lg" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail Rapat"></i>
                </a>';

            if (Auth::user()->pegawai->username === $rapat->pegawai_username || Auth::user()->pegawai->username === $rapat->pimpinan_username) {
                $aksi .= '<a href="' . url('rapat/agenda-rapat/' . $rapat->slug . '/edit') . '" class="mx-2 my-2">
                        <i class="fas fa-edit fa-lg" style="color: #FFD43B;" title="Edit Rapat"></i>
                      </a>';

                if (in_array($rapat->status, ['CANCELED', 'SCHEDULED'])) {
                    $aksi .= '<a href="' . url('rapat/agenda-rapat/' . $rapat->slug . '/batal') . '" onclick="return batalkanRapat(event,this.href,\'' . $rapat->status . '\')">
                            <i class="fas ' . $statusKeaktifan[$rapat->status][0] . ' fa-lg"
                               style="color: ' . $statusKeaktifan[$rapat->status][1] . ';"
                               title="Batalkan / Jadwal Ulang"></i>
                          </a>';
                }
            }

            if (Auth::user()->pegawai->username === $rapat->notulis_username && $rapat->status !== 'CANCELED' && $rapat->status == StatusAgendaRapat::STARTED->value) {
                $aksi .= '<a href="' . url('rapat/agenda-rapat/notulis/' . $rapat->slug . '/unggah-notulen') . '" class="btn btn-success btn-sm mx-2">Isi Notulen</a>';
            }
            $tugas = '';
            if (
                in_array(Auth::user()->pegawai->username, [$rapat->notulis_username, $rapat->pimpinan_username]) &&
                in_array($rapat->status, [StatusAgendaRapat::COMPLETED->value, StatusAgendaRapat::STARTED->value])
            ) {
                $tugas = '<a href="' . url('rapat/agenda-rapat/' . $rapat->slug . '/tugas') . '">
                        <span class="badge bg-primary p-2">Input Tugas</span>
                      </a>';
            }
            //untuk menyembunyikan rapat yang sudah selesai dan bukan notulis
            if ($rapat->status == StatusAgendaRapat::COMPLETED->value && Auth::user()->pegawai->username !== $rapat->notulis_username) {
                continue;
            }
            if ($rapat->rapatTindakLanjut()->exists() && $rapat->rapatNotulen()->exists()) {
                continue;
            }
            $data[] = [
                '<div class="text-center">' . ($index + 1) . '</div>',
                $rapat->agenda_rapat,
                '<div class="text-center">' . $startTime . '</div>',
                '<div class="text-center">' . $statusBadge . '</div>',
                '<div class="text-center">' . $aksi . '</div>',
            ];
            if ($showTugasColumn) {
                $data[$index][] = '<div class="text-center">' . $tugas . '</div>';
            }
        }

        $heads = [
            ['label' => 'No', 'width' => 6, 'class' => 'text-center'],
            ['label' => 'Agenda Rapat', 'width' => 25],
            ['label' => 'Waktu Mulai', 'width' => 25, 'class' => 'text-center'],
            ['label' => 'Status', 'width' => 10, 'class' => 'text-center'],
            ['label' => 'Aksi', 'width' => 20, 'class' => 'text-center'],
        ];

        $config = [
            'data'    => $data,
            'columns' => [
                ['className' => 'text-center'],
                null,
                ['className' => 'text-center'],
                ['className' => 'text-center'],
                ['className' => 'text-center', 'orderable' => false],
                // ['className' => 'text-center', 'orderable' => false],
            ],
        ];
        if ($showTugasColumn) {
            array_push($heads, ['label' => 'Tugas', 'width' => 10, 'class' => 'text-center']);
            array_push($config['columns'], ['className' => 'text-center', 'orderable' => false]);
        }
        return ['heads' => $heads, 'config' => $config];
    }
}
