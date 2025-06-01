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
        @php
            \Carbon\Carbon::setLocale('id');
        @endphp

        <!-- Header Section -->
        <div class="card-header bg-gradient-warning text-dark">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bold">
                    <i class="fas fa-tasks mr-2"></i>
                    Detail Tugas Tindak Lanjut
                </h5>
                <span class="badge badge-light">
                    <i class="fas fa-info-circle mr-1"></i>
                    Detail Tugas
                </span>
            </div>
        </div>

        <!-- Body -->
        <div class="card-body px-4 py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">

                    <!-- Link Tugas -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-dark mb-2">
                            <i class="fas fa-link mr-2"></i>Link Tugas
                        </h6>
                        <div class="bg-light p-3 rounded shadow-sm">
                            <a href="{{ $tindakLanjut->tugas ?: '#' }}" target="_blank" class="text-primary">
                                {{ $tindakLanjut->tugas ?: '-' }}
                            </a>
                        </div>
                    </div>

                    <!-- Kendala -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-dark mb-2">
                            <i class="fas fa-exclamation-circle mr-2"></i>Kendala Dalam Mengerjakan Tugas
                        </h6>
                        <div class="bg-light p-3 rounded shadow-sm">
                            <p class="mb-0">{{ $tindakLanjut->kendala ?: 'Tidak ada kendala yang dilaporkan.' }}</p>
                        </div>
                    </div>
                    <!-- File Dilampirkan -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-dark mb-2">
                            <i class="fas fa-paperclip mr-2"></i>File Yang Dilampirkan
                        </h6>
                        <ul class="list-group list-group-flush shadow-sm border rounded">
                            @forelse ($tindakLanjut->rapatTindakLanjutFile as $file)
                                @php
                                    $extension = pathinfo($file->nama_file, PATHINFO_EXTENSION);
                                    $icon = $icons[$extension] ?? ['icon' => 'fas fa-file', 'color' => '#A9A9A9'];
                                @endphp
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="{{ $icon['icon'] }} text-lg mr-2" style="color: {{ $icon['color'] }}"></i>
                                    <a href="{{ url('/rapat/agenda-rapat/download') }}" target="_blank" class="text-dark">
                                        {{ $file->nama_file }}
                                    </a>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">Tidak ada file dilampirkan</li>
                            @endforelse
                        </ul>
                    </div>
                    <!-- Form Penilaian untuk Pimpinan -->
                    @if ($tindakLanjut->rapatAgenda->pimpinan_username == Auth::user()->pegawai->username)
                        <form
                            action="{{ url('/rapat/tindak-lanjut-rapat/' . $tindakLanjut->slug . '/detail/simpan-tugas') }}"
                            method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="komentar_penugasan" class="form-label font-weight-bold">Komentar
                                    Penugasan</label>
                                <textarea name="komentar_penugasan" rows="3"
                                    class="form-control @error('komentar_penugasan') is-invalid @enderror" id="komentar_penugasan-tugas">{{ $tindakLanjut->komentar }}</textarea>
                                @error('komentar_penugasan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="kriteria_penilaian" class="form-label font-weight-bold">Kriteria
                                    Penilaian</label>
                                <select name="kriteria_penilaian"
                                    class="form-control @error('kriteria_penilaian') is-invalid @enderror">
                                    <option value="" selected disabled>--- Pilih Kriteria Penilaian ---</option>
                                    @foreach (KriteriaPenilaian::cases() as $kriteria)
                                        @continue($kriteria->value == KriteriaPenilaian::BELUM_DINILAI->value)
                                        <option value="{{ $kriteria->value }}"
                                            {{ $tindakLanjut->penilaian == $kriteria->value ? 'selected' : '' }}>
                                            {{ $kriteria->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kriteria_penilaian')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Simpan
                                </button>
                            </div>
                        </form>
                    @endif

                    <!-- Tampilan Komentar & Penilaian untuk Semua Role yang Diperbolehkan -->
                    @if (
                        ($tindakLanjut->pegawai_username == Auth::user()->pegawai->username &&
                            $tindakLanjut->penilaian != KriteriaPenilaian::BELUM_DINILAI->value) ||
                            (RoleGroupHelper::userHasRoleGroup(Auth::user(), RoleGroupHelper::pimpinanRoles()) &&
                                $tindakLanjut->penilaian != KriteriaPenilaian::BELUM_DINILAI->value))
                        <hr class="my-5">
                        <div class="bg-light p-4 rounded shadow-sm">
                            <h6 class="font-weight-bold text-dark mb-3">
                                <i class="fas fa-check-circle mr-2 text-success"></i>
                                Penilaian Tugas
                            </h6>

                            <div class="mb-3">
                                <label class="form-label">Komentar Penugasan</label>
                                <textarea class="form-control" disabled rows="3">{{ $tindakLanjut->komentar }}</textarea>
                            </div>

                            <div>
                                <label class="form-label">Kriteria Penilaian</label>
                                <select disabled class="form-control">
                                    @foreach (KriteriaPenilaian::cases() as $kriteria)
                                        @continue($kriteria->value == KriteriaPenilaian::BELUM_DINILAI->value)
                                        <option value="{{ $kriteria->value }}"
                                            {{ $tindakLanjut->penilaian == $kriteria->value ? 'selected' : '' }}>
                                            {{ $kriteria->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </x-adminlte-card>


@endsection

@push('js')
@endpush
