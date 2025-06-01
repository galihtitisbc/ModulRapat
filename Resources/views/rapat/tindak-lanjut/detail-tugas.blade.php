@extends('adminlte::page')
@section('title', 'Tugaskan Peserta Rapat')
{{-- @section('plugins.Select2', true) --}}
@section('content_header')
    <h5 class="m-0 text-dark">Tugaskan Peserta Rapat</h5>
@stop

@push('css')
@endpush

@section('content')
    @php
        use Modules\Rapat\Http\Helper\KriteriaPenilaian;
        use Modules\Rapat\Http\Helper\RoleGroupHelper;
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
    <x-adminlte-card title="Detail Tugas" theme="primary" icon="fas fa-tasks">
        <!-- Baris Link Tugas -->
        <div class="col-lg-9 col-md-12 col-sm-12 mx-auto">
            <div class="row mb-4">
                <div class="col-md-3 font-weight-bold">
                    Link Tugas :
                </div>
                <div class="col-md-9">
                    <a href="{{ $tindakLanjut->tugas ? $tindakLanjut->tugas : '#' }}" target="_blank">
                        {{ $tindakLanjut->tugas ? $tindakLanjut->tugas : '-' }}
                    </a>
                </div>
            </div>

            <!-- Baris File yang Dilampirkan -->
            <div class="row mb-4">
                <div class="col-md-3 font-weight-bold">
                    File Yang Dilampirkan :
                </div>
                <div class="col-md-9">
                    <ul class="list-group">
                        @if ($tindakLanjut->rapatTindakLanjutFile->isNotEmpty())
                            @foreach ($tindakLanjut->rapatTindakLanjutFile as $file)
                                @php
                                    $extension = pathinfo($file->nama_file, PATHINFO_EXTENSION);
                                    $icon = $icons[$extension] ?? [
                                        'icon' => 'fas fa-file',
                                        'color' => '#A9A9A9',
                                    ];
                                @endphp
                                <li class="list-group-item">
                                    <a href="{{ url('/rapat/agenda-rapat/download') }}" target="_blank">
                                        <i class="{{ $icon['icon'] }}" style="color: {{ $icon['color'] }};"></i>
                                        {{ $file->nama_file }}
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Baris Kendala Dalam Mengerjakan Tugas -->
            <div class="row mb-4">
                <div class="col-md-3 font-weight-bold">
                    Kendala Dalam Mengerjakan Tugas :
                </div>
                <div class="col-md-9">
                    <p>{{ $tindakLanjut->kendala }}</p>
                </div>
            </div>

            <!-- Form Komentar dan Penilaian (untuk pimpinan rapat) -->
            @if ($tindakLanjut->rapatAgenda->pimpinan_username == Auth::user()->pegawai->username)
                <hr>
                <form action="{{ url('/rapat/tindak-lanjut-rapat/' . $tindakLanjut->slug . '/detail/simpan-tugas') }}"
                    method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="komentar_penugasan">Komentar Penugasan :</label>
                        <textarea class="form-control @error('komentar_penugasan') is-invalid @enderror" name="komentar_penugasan"
                            id="komentar_penugasan" rows="3">{{ $tindakLanjut->komentar }}</textarea>
                        @error('komentar_penugasan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="kriteria_penilaian">Kriteria Penilaian :</label>
                        <select class="form-control @error('kriteria_penilaian') is-invalid @enderror"
                            name="kriteria_penilaian" id="kriteria_penilaian">
                            <option value="" selected>--- Pilih Kriteria Penilaian ---</option>
                            @foreach (KriteriaPenilaian::cases() as $kriteria)
                                @if ($kriteria->value == KriteriaPenilaian::BELUM_DINILAI->value)
                                    @continue
                                @endif
                                <option value="{{ $kriteria->value }}"
                                    {{ $tindakLanjut->penilaian == $kriteria->value ? 'selected' : '' }}>
                                    {{ $kriteria->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('kriteria_penilaian')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            @endif

            <!-- Tampilan Read-Only (untuk peserta/pegawai dan pimpinan setelah penilaian tersedia) -->
            @if (
                ($tindakLanjut->pegawai_username == Auth::user()->pegawai->username &&
                    $tindakLanjut->penilaian != KriteriaPenilaian::BELUM_DINILAI->value) ||
                    (RoleGroupHelper::userHasRoleGroup(Auth::user(), RoleGroupHelper::pimpinanRoles()) &&
                        $tindakLanjut->penilaian != KriteriaPenilaian::BELUM_DINILAI->value))
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="komentar_penugasan_readonly">Komentar Penugasan :</label>
                            <textarea class="form-control" id="komentar_penugasan_readonly" readonly rows="3">{{ $tindakLanjut->komentar }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="kriteria_penilaian_readonly">Kriteria Penilaian :</label>
                            <select class="form-control" id="kriteria_penilaian_readonly" disabled>
                                @foreach (KriteriaPenilaian::cases() as $kriteria)
                                    @if ($kriteria->value == KriteriaPenilaian::BELUM_DINILAI->value)
                                        @continue
                                    @endif
                                    <option value="{{ $kriteria->value }}"
                                        {{ $tindakLanjut->penilaian == $kriteria->value ? 'selected' : '' }}>
                                        {{ $kriteria->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </x-adminlte-card>

@endsection

@push('js')
@endpush
