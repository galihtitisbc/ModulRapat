@extends('adminlte::page')
@section('title', 'Rapat')
{{-- @section('plugins.Select2', true) --}}
@section('content_header')
    <h1 class="m-0 text-dark"></h1>
@stop

@push('css')
@endpush

@section('content')
    @php
        \Carbon\Carbon::setLocale('id');
    @endphp
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-info">
                        <div class="row">
                            <div class="col-lg-4 col-md-12 col-sm-12">
                                <x-adminlte-info-box title="Penyelesaian Tindak Lanjut Rapat"
                                    text="{{ $totalTugasSelesai }}/{{ $totalTugas }}" icon="fas fa-lg fa-tasks text-dark"
                                    theme="warning" icon-theme="white" :progress="$totalTugas > 0 ? ($totalTugasSelesai / $totalTugas) * 100 : 0" progress-theme="white" />
                            </div>
                            <div class="col-lg-4 col-md-12 col-sm-12">
                                <x-adminlte-info-box title="Total Rapat Mendatang" text="{{ $totalRapatMendatang }}"
                                    icon="fas fa-lg fa-clipboard-list text-dark" theme="blue" icon-theme="white" />
                            </div>
                            <div class="col-lg-4 col-md-12 col-sm-12">
                                </h1>
                                <x-adminlte-info-box title="Total Hadir Rapat"
                                    text="{{ $totalHadirRapat }}/{{ $totalKeseluruhanRapat }}"
                                    icon="fas fa-lg fa-calendar-check text-dark" :progress="$totalKeseluruhanRapat > 0
                                        ? ($totalHadirRapat / $totalKeseluruhanRapat) * 100
                                        : 0" progress-theme="white"
                                    theme="success" icon-theme="white" />
                            </div>
                        </div>
                    </div>
                    <div class="long-info mt-5">
                        @if ($tugasMendatang !== null)
                            <div class="position-relative mb-5">
                                <div class="bg-gradient-warning text-dark p-4 rounded shadow">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h5 class="mb-3">Tindak Lanjut Rapat Dengan Deadline Segera :</h5>
                                            <a href="{{ url('/rapat/tindak-lanjut-rapat/tugas/' . $tugasMendatang->slug . '/unggah-tugas') }}"
                                                class="text-dark">
                                                <h5 class="font-weight-bold mb-2">{{ $tugasMendatang->deskripsi_tugas }}
                                                </h5>
                                            </a>
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-clock mr-2"></i>
                                                <span>{{ \Carbon\Carbon::parse($tugasMendatang->batas_waktu)->translatedFormat('l, d F Y') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-md-right">
                                            <div class="badge badge-light p-2">
                                                <i class="fas fa-calendar-alt mr-1 text-dark"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="empty-state">
                                <p><i class="fas fa-info-circle"></i> Tidak Ada Tugas Yang Belum Selesai</p>
                            </div>
                        @endif
                        @if ($rapat !== null)
                            <div class="position-relative mb-5">
                                <div class="bg-gradient-primary text-white p-4 rounded shadow">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h5 class="mb-3">Jadwal Agenda Rapat Mendatang :</h5>
                                            <a href="{{ url('rapat/agenda-rapat/' . $rapat->slug . '/detail') }}"
                                                class="text-white">
                                                <h5 class="font-weight-bold mb-2">{{ $rapat->agenda_rapat }}</h5>
                                            </a>
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-clock mr-2"></i>
                                                <span>{{ \Carbon\Carbon::parse($rapat->waktu_mulai)->translatedFormat('l, d F Y') }}</span>
                                                <span class="mx-2">|</span>
                                                <span>{{ \Carbon\Carbon::parse($rapat->waktu_mulai)->translatedFormat('H:i') }}
                                                    WIB</span>
                                            </div>
                                            <div class="mt-1">
                                                <i class="fas fa-map-marker-alt mr-2"></i>
                                                <span>{{ $rapat->tempat }}</span>
                                                <br>
                                                @if ($rapat->tempat == 'zoom')
                                                    <i class="fas fa-link mt-2">
                                                        <span class="ml-1">
                                                            <a class="text-white" href="{{ $rapat->zoom_link }}"
                                                                target="_blank">{{ $rapat->zoom_link }}</a>
                                                        </span>
                                                    </i>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-md-right">
                                            <div class="badge badge-light p-2">
                                                <i class="fas fa-calendar-alt mr-1 text-dark"></i>
                                                <span class="text-dark font-weight-bold">Dijadwalkan</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="empty-state">
                                <p><i class="fas fa-info-circle"></i> Tidak Ada Agenda Rapat Mendatang</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script></script>
@endpush
