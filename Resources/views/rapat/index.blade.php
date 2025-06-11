@extends('adminlte::page')
@section('title', 'Rapat')
{{-- @section('plugins.Select2', true) --}}
@section('content_header')
    <h1 class="m-0 text-dark">List Agenda Rapat</h1>
@stop

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
@endpush

@section('content')
    @php
        use Modules\Rapat\Http\Helper\RoleGroupHelper;
        use Modules\Rapat\Http\Helper\StatusAgendaRapat;
        use Carbon\Carbon;
        Carbon::setLocale('id');
        $statusRapat = [
            'CANCELED' => ['danger', 'Di Batalkan'],
            'SCHEDULED' => ['warning', 'Di Jadwalkan'],
            'COMPLETED' => ['success', 'Selesai'],
            'STARTED' => ['primary', 'Sedang Berlangsung'],
        ];

        $statusKeaktifan = [
            'SCHEDULED' => ['fa-calendar-times', '#ff0000'],
            'CANCELED' => ['fa-undo', '#5cb85c'],
            'COMPLETED' => ['fas fa-check-circle', '#28a745'],
            'STARTED' => ['fas fa-play-circle', '#0275d8'],
        ];
        $showTugasColumn = $rapats->getCollection()->contains(function ($rapat) {
            return $rapat->pimpinan_username === Auth::user()->pegawai->username ||
                $rapat->notulis_username === Auth::user()->pegawai->username;
        });

    @endphp
    <x-adminlte-card>
        {{-- jika user adalah pimpinan rapat, dan ketua panitia, maka button create rapat akan muncul --}}
        @if (RoleGroupHelper::userHasRoleGroup(Auth::user(), RoleGroupHelper::pimpinanRapatRoles()) ||
                Auth::user()->pegawai->ketuaPanitia->isNotEmpty())
            <div class="btn-tambah d-flex justify-content-end my-2">
                <a href="{{ url('rapat/agenda-rapat/create') }}" class="btn btn-primary">Tambah Rapat</a>
            </div>
        @endif
        <form action="" method="get">
            <div class="col-lg-10 col-md-12 col-sm-12 mx-auto my-3">
                <div class="row d-flex justify-content-center">
                    <div class="col-lg-5 col-md-12 col-sm-12 mb-2">
                        <input type="text" name="agenda_rapat" class="form-control mb-2" placeholder="Cari Agenda Rapat"
                            value="{{ request('agenda_rapat') }}">
                        <select name="status" class="form-control" id="" onchange="this.form.submit()">
                            <option value="" selected>-- Pilih Status --</option>
                            <option value="{{ StatusAgendaRapat::STARTED->value }}"
                                {{ request('status') == 'STARTED' ? 'selected' : '' }}>
                                {{ StatusAgendaRapat::STARTED->label() }}
                            </option>
                            <option value="{{ StatusAgendaRapat::SCHEDULED->value }}"
                                {{ request('status') == 'SCHEDULED' ? 'selected' : '' }}>
                                {{ StatusAgendaRapat::SCHEDULED->label() }}
                            </option>
                            <option value="{{ StatusAgendaRapat::CANCELLED->value }}"
                                {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>
                                {{ StatusAgendaRapat::CANCELLED->label() }}
                            </option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-12 col-sm-12 mb-2">
                        <input type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="dari_tgl"
                            class="form-control mb-2" value="{{ request('dari_tgl') }}" placeholder="Rapat Dari Tanggal">
                        <input type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="sampai_tgl"
                            class="form-control" value="{{ request('sampai_tgl') }}" placeholder="Rapat Sampai Tanggal">
                    </div>
                    <div class="col-lg-1 col-md-12 col-sm-12">
                        <button class="btn btn-primary col-sm-12"><i class="fas fa-search"></i></button>
                        @if (request('agenda_rapat') || request('dari_tgl') || request('sampai_tgl') || request('status'))
                            <button type="button"
                                onclick="this.form.reset(); window.location='{{ url('rapat/agenda-rapat') }}'"
                                class="btn btn-danger mt-2 col-sm-12">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table">
                <thead class="table-light text-center">
                    <tr>
                        <th style="width: 6%;">No</th>
                        <th style="width: 25%;">Agenda Rapat</th>
                        <th style="width: 25%;">Waktu Mulai</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 20%;">Aksi</th>
                        @if ($showTugasColumn)
                            <th style="width: 10%;">Tugas</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rapats as $index => $rapat)
                        @php
                            if (
                                $rapat->status == StatusAgendaRapat::COMPLETED->value &&
                                Auth::user()->pegawai->username !== $rapat->notulis_username &&
                                Auth::user()->pegawai->username !== $rapat->pimpinan_username
                            ) {
                                continue;
                            }
                            if ($rapat->rapatTindakLanjut()->exists() && $rapat->rapatNotulen()->exists()) {
                                continue;
                            }
                            if ($rapat->is_penugasan !== null && $rapat->rapatNotulen()->exists()) {
                                continue;
                            }

                            $startTime = Carbon::parse($rapat->waktu_mulai)->translatedFormat('l, d F Y H:i');
                            $statusBadge =
                                '<span class="badge bg-' .
                                $statusRapat[$rapat->status][0] .
                                '">' .
                                $statusRapat[$rapat->status][1] .
                                '</span>';

                            $aksi =
                                '<a href="' .
                                url('rapat/agenda-rapat/' . $rapat->slug . '/detail') .
                                '">
                                        <i class="fas fa-eye fa-lg" title="Detail Rapat"></i>
                                    </a>';

                            if (
                                Auth::user()->pegawai->username === $rapat->pegawai_username ||
                                Auth::user()->pegawai->username === $rapat->pimpinan_username
                            ) {
                                if (
                                    in_array($rapat->status, [
                                        StatusAgendaRapat::CANCELLED->value,
                                        StatusAgendaRapat::SCHEDULED->value,
                                    ])
                                ) {
                                    $aksi .=
                                        '<a href="' .
                                        url('rapat/agenda-rapat/' . $rapat->slug . '/edit') .
                                        '" class="mx-2 my-2">
                                                <i class="fas fa-edit fa-lg" style="color: #FFD43B;" title="Edit Rapat"></i>
                                              </a>';
                                    $aksi .=
                                        '<a href="' .
                                        url('rapat/agenda-rapat/' . $rapat->slug . '/batal') .
                                        '" onclick="return batalkanRapat(event,this.href,\'' .
                                        $rapat->status .
                                        '\')">
                                                <i class="fas ' .
                                        $statusKeaktifan[$rapat->status][0] .
                                        ' fa-lg"
                                                   style="color: ' .
                                        $statusKeaktifan[$rapat->status][1] .
                                        ';" title="Batalkan / Jadwal Ulang"></i>
                                              </a>';
                                }
                            }

                            if (
                                Auth::user()->pegawai->username === $rapat->notulis_username &&
                                $rapat->status !== StatusAgendaRapat::CANCELLED->value &&
                                $rapat->status == StatusAgendaRapat::STARTED->value
                            ) {
                                $aksi .=
                                    '<a href="' .
                                    url('rapat/agenda-rapat/notulis/' . $rapat->slug . '/unggah-notulen') .
                                    '" class="btn btn-success btn-sm mx-2">Isi Notulen</a>';
                            }
                            $tugas = '';
                            if (
                                $rapat->is_penugasan === null &&
                                in_array(Auth::user()->pegawai->username, [
                                    $rapat->notulis_username,
                                    $rapat->pimpinan_username,
                                ]) &&
                                in_array($rapat->status, [
                                    StatusAgendaRapat::COMPLETED->value,
                                    StatusAgendaRapat::STARTED->value,
                                ])
                            ) {
                                $tugas =
                                    '<a href="' .
                                    url('rapat/agenda-rapat/' . $rapat->slug . '/tugas') .
                                    '">
                                            <span class="badge bg-primary p-2">Input Tugas</span>
                                          </a>';
                            }
                        @endphp

                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $rapat->agenda_rapat }}</td>
                            <td class="text-center">{{ $startTime }}</td>
                            <td class="text-center">{!! $statusBadge !!}</td>
                            <td class="text-center">{!! $aksi !!}</td>
                            @if ($showTugasColumn)
                                <td class="text-center">{!! $tugas !!}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-center">
                {{ $rapats->links() }}
            </div>
        </div>
    </x-adminlte-card>
@endsection

@push('js')

    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl =>
            new bootstrap.Tooltip(tooltipTriggerEl)
        );

        function batalkanRapat(event, url, status) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: status == "SCHEDULED" ? "Rapat akan dibatalkan!" : "Rapat akan dijadwalkan kembali!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: status == "SCHEDULED" ? 'Ya, Batalkan Rapat!' : 'Ya, Jadwalkan Kembali!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            })
        }
    </script>
    @if (session('swal'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "{{ session('swal.title') }}",
                    text: "{{ session('swal.text') }}",
                    icon: "{{ session('swal.icon') }}"
                });
            });
        </script>
    @endif
@endpush
