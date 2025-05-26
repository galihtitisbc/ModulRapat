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
    <x-adminlte-card>
        <h4 class="text-center mb-4"></h4>
        <div class="row col-lg-8 mx-auto mt-5">
            <div class="col-lg-3 col-sm-4 col-md-4 font-weight-bold">
                Link Tugas :
                <hr>
            </div>
            <div class="col-8">
                <a href="{{ $tindakLanjut->tugas ? $tindakLanjut->tugas : '#' }}"
                    target="_blank">{{ $tindakLanjut->tugas ? $tindakLanjut->tugas : '-' }}</a>
                <hr>
            </div>
        </div>
        <div class="row col-lg-8 mx-auto">
            <div class="col-lg-3 col-sm-4 col-md-4 font-weight-bold">
                File Yang Dilampirkan :
                <hr>
            </div>
            <div class="col-8">
                <ul class="list-group list-group-flush">
                    @if ($tindakLanjut->rapatTindakLanjutFile->isNotEmpty())
                        @foreach ($tindakLanjut->rapatTindakLanjutFile as $file)
                            @php
                                $extension = pathinfo($file->nama_file, PATHINFO_EXTENSION);
                                $icon = $icons[$extension] ?? [
                                    'icon' => 'fas fa-file',
                                    'color' => '#A9A9A9',
                                ];
                            @endphp
                            <li class="list-group-item"><a href="{{ url('/rapat/agenda-rapat/download') }}"
                                    target="_blank"><i class="{{ $icon['icon'] }}"
                                        style="color: {{ $icon['color'] }}; fa-lg  mr-2"></i>
                                    {{ $file->nama_file }}</a></li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
        <div class="row col-lg-8 mx-auto mt-4">
            <div class="col-lg-3 col-sm-4 col-md-4 font-weight-bold">
                Kendala Dalam Mengerjakan Tugas :
                <hr>
            </div>
            <div class="col-8">
                <p>{{ $tindakLanjut->kendala }}</p>
                <hr>
            </div>
        </div>
        @if ($tindakLanjut->rapatAgenda->pimpinan_username == Auth::user()->pegawai->username)
            <hr>
            <form class="col-8 mx-auto"
                action="{{ url('/rapat/tindak-lanjut-rapat/' . $tindakLanjut->slug . '/detail/simpan-tugas') }}"
                method="POST">
                @csrf
                <div class="mb-3">
                    <label for="deskripsi-tugas" class="form-label">Komentar Penugasan :</label>
                    <textarea class="form-control @error('komentar_penugasan') is-invalid @enderror" name="komentar_penugasan"
                        id="komentar_penugasan-tugas" rows="3">{{ $tindakLanjut->komentar }}</textarea>
                    @error('komentar_penugasan')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="exampleFormControlSelect1">Kriteria Penilaian :</label>
                    <select class="form-control @error('kriteria_penilaian') is-invalid @enderror"
                        name="kriteria_penilaian">
                        <option value="" selected>--- Pilih Kriteria Penilaian ---</option>
                        @foreach (KriteriaPenilaian::cases() as $kriteria)
                            @if ($kriteria->value == KriteriaPenilaian::BELUM_DINILAI->value)
                                @php
                                    continue;
                                @endphp
                            @endif
                            <option value="{{ $kriteria->value }}"
                                {{ $tindakLanjut->penilaian == $kriteria->value ? 'selected' : '' }}>
                                {{ $kriteria->label() }}</option>
                        @endforeach
                    </select>
                    @error('kriteria_penilaian')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mx-auto text-center">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        @endif
        {{-- menampilkan penilaian terhadap tugas yang sudah dinilai oleh pimpinan rapat, yang bisa melihat penilaian nya adalah pimpinan, peserta rapat, dan pimpinan rapat --}}
        @if (
            ($tindakLanjut->pegawai_username == Auth::user()->pegawai->username &&
                $tindakLanjut->penilaian != KriteriaPenilaian::BELUM_DINILAI->value) ||
                (RoleGroupHelper::userHasRoleGroup(Auth::user(), RoleGroupHelper::pimpinanRoles()) &&
                    $tindakLanjut->penilaian != KriteriaPenilaian::BELUM_DINILAI->value))
            <hr>
            <div class="col-8 mx-auto">
                <div class="mb-3">
                    <label for="deskripsi-tugas" class="form-label">Komentar Penugasan :</label>
                    <textarea class="form-control" disabled rows="3">{{ $tindakLanjut->komentar }}</textarea>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlSelect1">Kriteria Penilaian :</label>
                    <select disabled class="form-control @error('kriteria_penilaian') is-invalid @enderror"
                        name="kriteria_penilaian">
                        @foreach (KriteriaPenilaian::cases() as $kriteria)
                            @if ($kriteria->value == KriteriaPenilaian::BELUM_DINILAI->value)
                                @php
                                    continue;
                                @endphp
                            @endif
                            <option value="{{ $kriteria->value }}"
                                {{ $tindakLanjut->penilaian == $kriteria->value ? 'selected' : '' }}>
                                {{ $kriteria->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
    </x-adminlte-card>
@endsection

@push('js')
@endpush
