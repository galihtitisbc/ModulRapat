@extends('adminlte::page')
@section('title', 'Unggah Notulen')
{{-- @section('plugins.Select2', true) --}}
@section('content_header')
    <h5 class="m-0 text-dark">Unggah Notulen</h5>
@stop

@push('css')
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <style>
        .trix-button-group.trix-button-group--file-tools {
            display: none;
        }
    </style>
@endpush

@section('content')
    @php
        use Modules\Rapat\Http\Helper\StatusPesertaRapat;
    @endphp
    <x-adminlte-card>
        @php

            $statusKehadiran = [
                'BERSEDIA' => 'primary',
                'TIDAK_BERSEDIA' => 'danger',
                'HADIR' => 'success',
                'TIDAK_HADIR' => 'secondary',
                'MENUNGGU' => 'warning',
            ];
            $heads = [
                'No',
                'Nama Peserta',
                ['label' => 'Email'],
                ['label' => 'Status Konfirmasi'],
                ['label' => 'Hadir'],
            ];
            $data = [];
            foreach ($agendaRapat->rapatAgendaPeserta as $key => $rapat) {
                $checkBox =
                    ' <input class="form-check-input" type="checkbox" name="peserta_hadir[]" value="' .
                    $rapat->username .
                    '"' .
                    (in_array($rapat->username, old('peserta_hadir', [])) ? ' checked' : '') .
                    '>';
                $status =
                    '<span class="badge badge-' .
                    $statusKehadiran[$rapat->pivot->status] .
                    '">' .
                    StatusPesertaRapat::from($rapat->pivot->status)->label() .
                    '</span>';
                $data[] = [$key + 1, $rapat->formatted_name, $rapat->user->email, $status, $checkBox];
            }
            $config = [
                'data' => $data,
                'order' => [[0, 'asc']],
                'columns' => [
                    ['className' => 'text-center'],
                    null,
                    ['orderable' => false],
                    ['className' => 'text-center'],
                    ['className' => 'text-center', 'orderable' => false],
                ],
            ];
        @endphp
        <div class="col-sm-12 col-lg-9 mx-auto">
            <!-- Agenda Rapat Title -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="text-center">
                        <h5 class="text-primary mb-2">Agenda Rapat:</h5>
                        <h4 class="font-weight-bold text-dark border-bottom border-primary pb-2 d-inline-block">
                            {{ $agendaRapat->agenda_rapat }}
                        </h4>
                    </div>
                </div>
            </div>
            <hr>

            <form action="{{ url('/rapat/agenda-rapat/notulis/' . $agendaRapat->slug . '/unggah-notulen') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                {{-- Error Handling --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Terjadi kesalahan:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Informasi Agenda --}}
                <x-adminlte-callout theme="info">
                    <div class="row text-center">
                        <div class="col-lg-4 col-sm-12 mb-3">
                            <p class="mb-1" style="font-size: 1.2rem">Total Peserta Keseluruhan :</p>
                            <span class="btn btn-primary px-4">{{ $agendaRapat->rapatAgendaPeserta->count() }}</span>
                        </div>
                        <div class="col-lg-4 col-sm-12 mb-3">
                            <p class="mb-1" style="font-size: 1.2rem">Total Peserta Bersedia Hadir :</p>
                            <span class="btn btn-success px-4">
                                {{ $agendaRapat->rapatAgendaPeserta->where('pivot.status', StatusPesertaRapat::BERSEDIA->value)->count() }}
                            </span>
                        </div>
                        <div class="col-lg-4 col-sm-12 mb-3">
                            <p class="mb-1" style="font-size: 1.2rem">Pimpinan Rapat :</p>
                            <span class="btn btn-secondary px-4">
                                {{ $agendaRapat->rapatAgendaPimpinan->formatted_name }}
                            </span>
                        </div>
                    </div>
                </x-adminlte-callout>

                {{-- Daftar Peserta --}}
                <div class="mt-5">
                    <h4>Daftar Peserta :</h4>
                    <hr>
                    <x-adminlte-datatable id="table1" :heads="$heads" :config="$config">
                        @foreach ($config['data'] as $row)
                            <tr>
                                @foreach ($row as $cell)
                                    <td>{!! $cell !!}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                </div>

                {{-- Catatan Rapat --}}
                <div class="mt-5">
                    <h4>Catatan Rapat :</h4>
                    <hr>
                    <input id="x" type="hidden" name="catatan_rapat" value="{{ old('catatan_rapat') }}">
                    <trix-editor input="x"
                        placeholder="Masukkan Catatan Rapat atau Unggah File Jika Ada"></trix-editor>
                </div>

                {{-- Upload File Notulen --}}
                <div class="mt-5">
                    <x-adminlte-card title="Unggah File Notulen (Jika Ada):" theme="primary" icon="fas fa-file-upload">
                        <div class="form-group mb-0">
                            <input type="file" name="notulen_file[]" class="form-control-file" multiple>
                        </div>
                    </x-adminlte-card>
                </div>

                {{-- Upload Dokumentasi --}}
                <div class="mt-5">
                    <x-adminlte-card title="Unggah Dokumentasi Rapat:" theme="primary" icon="fas fa-image">
                        <div class="form-group mb-0">
                            <input type="file" name="dokumentasi_file[]" class="form-control-file" multiple>
                        </div>
                    </x-adminlte-card>
                </div>

                {{-- Submit Button --}}
                <div class="text-center mt-4">
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>

    </x-adminlte-card>
@endsection

@push('js')
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
@endpush
