@extends('adminlte::page')
@section('title', 'Kepanitiaan')
{{-- @section('plugins.Select2', true) --}}
@section('content_header')
    <h3 class="m-0 text-dark">Kepanitiaan</h3>
@stop

@push('css')
@endpush

@section('content')
    @php
        use Carbon\Carbon;
        Carbon::setLocale('id');
    @endphp
    <x-adminlte-card>
        <div class="container-fluid">
            <h2 class="text-center mb-4">Detail Kepanitiaan</h2>
            <!-- Informasi Dasar Kepanitiaan -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">{{ $panitia->nama_kepanitiaan }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar-alt mr-2"></i>Tanggal Mulai:</label>
                                        <p>{{ Carbon::parse($panitia->tanggal_mulai)->locale('id')->translatedFormat('d F Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar-check mr-2"></i>Tanggal Berakhir:</label>
                                        <p>{{ Carbon::parse($panitia->tanggal_berakhir)->locale('id')->format('d F Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-clipboard-list mr-2"></i>Deskripsi:</label>
                                        <p>{{ $panitia->deskripsi }}</p>
                                    </div>

                                    <div class="form-group">
                                        <label><i class="fas fa-bullseye mr-2"></i>Tujuan Kepanitiaan:</label>
                                        <p>{{ $panitia->tujuan }}</p>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-bullseye mr-2"></i>Surat Tugas Kepanitiaan:</label>
                                        <p><a href="{{ url('/rapat/panitia/download/' . $panitia->slug) }}"
                                                target="_blank">Unduh
                                                Surat Tugas Kepanitiaan</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-users mr-2"></i>Struktur Kepanitiaan</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr class="bg-light">
                                            <th width="5%">No</th>
                                            <th width="15%">NIP</th>
                                            <th width="30%">Nama</th>
                                            <th width="30%">Jabatan Dalam Kepanitiaan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>{{ optional($panitia->ketua)->nip }}</td>
                                            <td>{{ optional($panitia->ketua)->formatted_name }}</td>
                                            <td>Ketua Panitia</td>
                                        </tr>
                                        @forelse($struktur as $index => $anggota)
                                            <tr>
                                                <td>{{ $index + 1 + 1 }}</td>
                                                <td>{{ optional($anggota['pegawai'])->nip }}</td>
                                                <td>{{ optional($anggota['pegawai'])->formatted_name }}</td>
                                                <td>{{ $anggota['jabatan'] }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Tidak ada anggota kepanitiaan</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-users mr-2"></i>Riwayat Rapat Kepanitiaan</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr class="bg-light">
                                            <th width="5%">No</th>
                                            <th width="30%">Agenda Rapat</th>
                                            <th width="30%">Tanggal Rapat</th>
                                            <th width="30%" class="text-center">Laporan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($panitia->rapatAgenda as $index => $rapat)
                                            @if ($rapat->status == \Modules\Rapat\Http\Helper\StatusAgendaRapat::COMPLETED->value)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $rapat->agenda_rapat }}</td>
                                                    <td>{{ Carbon::parse($rapat->waktu_mulai)->translatedFormat('l, d F Y') }}
                                                    </td>
                                                    <td class="text-center"><a target="_blank"
                                                            href="{{ url('/rapat/riwayat-rapat/' . $rapat->slug . '/generate-pdf') }}">
                                                            Unduh Laporan Rapat
                                                        </a></td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Kepanitiaan Ini Tidak Memiliki
                                                    Riwayat Rapat</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </x-adminlte-card>
@endsection

@push('js')
@endpush
