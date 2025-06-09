@extends('adminlte::page')
@section('title', 'Rapat')
{{-- @section('plugins.Select2', true) --}}
@section('content_header')
@stop

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
@endpush

@section('content')
    @php
        use Modules\Rapat\Http\Helper\StatusPesertaRapat;
        \Carbon\Carbon::setLocale('id');
        $statusPeserta = [
            'BERSEDIA' => 'primary',
            'TIDAK_BERSEDIA' => 'danger',
            'HADIR' => 'success',
            'TIDAK_HADIR' => 'secondary',
            'MENUNGGU' => 'warning',
        ];
        $statusRapat = [
            'CANCELED' => ['danger', 'Dibatalkan', 'fas fa-times-circle'],
            'SCHEDULED' => ['warning', 'Dijadwalkan', 'fas fa-calendar-alt'],
            'COMPLETED' => ['success', 'Selesai', 'fas fa-check-circle'],
            'STARTED' => ['primary', 'Sedang Berlangsung', 'fas fa-play-circle'],
        ];
        $icons = [
            'jpg' => ['icon' => 'fas fa-file-image', 'color' => '#FFD700'],
            'jpeg' => ['icon' => 'fas fa-file-image', 'color' => '#FFD700'],
            'png' => ['icon' => 'fas fa-file-image', 'color' => '#FFD700'],
            'PNG' => ['icon' => 'fas fa-file-image', 'color' => '#FFD700'],
            'doc' => ['icon' => 'fas fa-file-word', 'color' => '#1E90FF'],
            'docx' => ['icon' => 'fas fa-file-word', 'color' => '#1E90FF'],
            'xls' => ['icon' => 'fas fa-file-excel', 'color' => '#008000'],
            'xlsx' => ['icon' => 'fas fa-file-excel', 'color' => '#008000'],
            'pdf' => ['icon' => 'fas fa-file-pdf', 'color' => '#FF0000'],
            'txt' => ['icon' => 'fas fa-file-alt', 'color' => '#808080'],
        ];
    @endphp

    <div class="card shadow-lg">
        <x-adminlte-card>
            <!-- Header Section with Background -->
            <div class="position-relative mb-5">
                <div class="bg-gradient-primary text-white p-4 rounded shadow">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="font-weight-bold mb-2">{{ $rapat->agenda_rapat }}</h5>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-clock mr-2"></i>
                                <span>{{ \Carbon\Carbon::parse($rapat->waktu_mulai)->translatedFormat('l, d F Y') }}</span>
                                <span class="mx-2">|</span>
                                <span>{{ \Carbon\Carbon::parse($rapat->waktu_mulai)->translatedFormat('H:i') }} WIB</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <span>{{ $rapat->tempat }}</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-right">
                            <div class="badge badge-light p-2 shadow-sm">
                                <i
                                    class="{{ $statusRapat[$rapat->status][2] }} mr-1 text-{{ $statusRapat[$rapat->status][0] }}"></i>
                                <span
                                    class="text-{{ $statusRapat[$rapat->status][0] }} font-weight-bold">{{ $statusRapat[$rapat->status][1] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="row">
                <!-- Left Side Information -->
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 border-left-primary shadow">
                        <div class="card-header bg-white">
                            <h5 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Informasi Rapat
                            </h5>
                        </div>
                        <div class="card-body">
                            @if ($rapat->rapatKepanitiaan)
                                <!-- Jika Rapat Kepanitiaan -->
                                <div class="media mb-4">
                                    <div class="mr-3">
                                        <div class="rounded-circle bg-success text-white text-center p-3"
                                            style="width: 50px; height: 50px;">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <h6 class="text-muted mb-1">Nama Kepanitiaan</h6>
                                        <p class="font-weight-bold"> {{ $rapat->rapatKepanitiaan->nama_kepanitiaan }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                            <!-- Pimpinan Rapat -->
                            <div class="media mb-4">
                                <div class="mr-3">
                                    <div class="rounded-circle bg-primary text-white text-center p-3"
                                        style="width: 50px; height: 50px;">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <h6 class="text-muted mb-1">Pimpinan Rapat</h6>
                                    <p class="font-weight-bold"> {{ $rapat->rapatAgendaPimpinan->formatted_name }}
                                    </p>
                                </div>
                            </div>

                            <!-- Notulis Rapat -->
                            <div class="media mb-4">
                                <div class="mr-3">
                                    <div class="rounded-circle bg-info text-white text-center p-3"
                                        style="width: 50px; height: 50px;">
                                        <i class="fas fa-pen"></i>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <h6 class="text-muted mb-1">Notulis Rapat</h6>
                                    <p class="font-weight-bold">{{ $rapat->rapatAgendaNotulis->formatted_name }}</p>
                                </div>
                            </div>

                            @if ($rapat->tempat == 'zoom')
                                <!-- Zoom Link -->
                                <div class="card mb-4 bg-light">
                                    <div class="card-body p-3">
                                        <h6 class="text-primary mb-2">
                                            <i class="fas fa-video mr-2"></i>Link Zoom
                                        </h6>
                                        <a href="{{ $rapat->zoom_link }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary btn-block">
                                            <i class="fas fa-external-link-alt mr-1"></i>Buka Zoom Meeting
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <!-- Calendar Link -->
                            <div class="card bg-light">
                                <div class="card-body p-3">
                                    <h6 class="text-primary mb-2">
                                        <i class="fas fa-calendar mr-2"></i>Google Calendar
                                    </h6>
                                    <a href="{{ $rapat->calendar_link }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary btn-block">
                                        <i class="fas fa-plus mr-1"></i>Tambahkan ke Kalender
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side Content -->
                <div class="col-lg-8">
                    <!-- Lampiran -->
                    <div class="card mb-4 border-left-warning shadow">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="m-0 font-weight-bold text-warning">
                                <i class="fas fa-paperclip mr-2"></i>Lampiran
                            </h5>
                            <span class="badge badge-light">{{ $rapat->rapatLampiran->count() }} file</span>
                        </div>
                        <div class="card-body">
                            @if ($rapat->rapatLampiran->isNotEmpty())
                                <div class="row">
                                    @foreach ($rapat->rapatLampiran as $lampiran)
                                        @php
                                            $extension = pathinfo($lampiran->nama_file, PATHINFO_EXTENSION);
                                            $icon = $icons[$extension] ?? [
                                                'icon' => 'fas fa-file',
                                                'color' => '#A9A9A9',
                                            ];
                                        @endphp
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 shadow-sm hover-shadow">
                                                <div class="card-body p-3 d-flex align-items-center">
                                                    <i class="{{ $icon['icon'] }} fa-2x mr-3"
                                                        style="color: {{ $icon['color'] }};"></i>
                                                    <div>
                                                        <h6 class="mb-1 text-truncate" style="max-width: 180px;"
                                                            title="{{ $lampiran->nama_file }}">
                                                            {{ $lampiran->nama_file }}
                                                        </h6>
                                                        <a href="{{ url('/rapat/agenda-rapat/' . $lampiran->nama_file . '/download') }}"
                                                            class="btn btn-sm btn-outline-secondary">
                                                            <i class="fas fa-download mr-1"></i>Download
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-folder-open text-muted fa-3x mb-3"></i>
                                    <p class="text-muted">Tidak ada lampiran untuk rapat ini</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Daftar Peserta -->
                    <div class="card border-left-success shadow">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="m-0 font-weight-bold text-success">
                                <i class="fas fa-users mr-2"></i>Daftar Peserta
                            </h5>
                            <span class="badge badge-light">{{ $rapat->rapatAgendaPeserta->count() }} peserta</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="daftar-peserta" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="5%">No</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th class="text-center" width="15%">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rapat->rapatAgendaPeserta as $peserta)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>
                                                    {{ $peserta->formatted_name }}
                                                </td>
                                                <td>{{ $peserta->user->email }}</td>
                                                <td class="text-center">
                                                    @php
                                                        $statusClass = $statusPeserta[$peserta->pivot->status];
                                                        $statusLabel = StatusPesertaRapat::from(
                                                            $peserta->pivot->status,
                                                        )->label();
                                                        $statusIcon = '';

                                                        // Menentukan ikon berdasarkan status
                                                        if (
                                                            $peserta->pivot->status ==
                                                            StatusPesertaRapat::BERSEDIA->value
                                                        ) {
                                                            $statusIcon = 'fas fa-check';
                                                        } elseif (
                                                            $peserta->pivot->status ==
                                                            StatusPesertaRapat::TIDAK_BERSEDIA->value
                                                        ) {
                                                            $statusIcon = 'fas fa-times';
                                                        } elseif (
                                                            $peserta->pivot->status == StatusPesertaRapat::HADIR->value
                                                        ) {
                                                            $statusIcon = 'fas fa-user-check';
                                                        } elseif (
                                                            $peserta->pivot->status ==
                                                            StatusPesertaRapat::TIDAK_HADIR->value
                                                        ) {
                                                            $statusIcon = 'fas fa-user-times';
                                                        } elseif (
                                                            $peserta->pivot->status ==
                                                            StatusPesertaRapat::MENUNGGU->value
                                                        ) {
                                                            $statusIcon = 'fas fa-clock';
                                                        }
                                                    @endphp

                                                    <span class="badge badge-pill bg-{{ $statusClass }} px-3 py-2">
                                                        <i class="{{ $statusIcon }} mr-1"></i>
                                                        {{ $statusLabel }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-adminlte-card>
    </div>
@endsection

@push('js')
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script>
        $(document).ready(function() {
            $('#daftar-peserta').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
@endpush
