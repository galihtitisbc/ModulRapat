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
                ['label' => 'Whatsapp'],
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
                $data[] = [$key + 1, $rapat->nama, '0989089890', $status, $checkBox];
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
            <h4 class="text-center">{{ $agendaRapat->agenda_rapat }}</h4>
            <hr>
            <form action="{{ url('/rapat/agenda-rapat/notulis/' . $agendaRapat->slug . '/unggah-notulen') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Terjadi kesalahan:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <x-adminlte-callout theme="info">
                    <div class="row d-flex justify-content-between">
                        <div class="col-lg-4 col-sm-12">
                            <div class="peserta-keseluruhan">
                                <span>
                                    <p style="font-size: 1.2rem">Total Peserta Keseluruhan :</p>
                                </span>
                                <button
                                    class="btn btn-primary px-4 mt-1">{{ $agendaRapat->rapatAgendaPeserta->count() }}</button>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <div class="peserta-hadir">
                                <span>
                                    <p style="font-size: 1.2rem">Total Peserta Bersedia Hadir :</p>
                                </span>
                                <button
                                    class="btn btn-success px-4 mt-1">{{ $agendaRapat->rapatAgendaPeserta->where('pivot.status', StatusPesertaRapat::BERSEDIA->value)->count() }}</button>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <div class="pimpinan">
                                <span>
                                    <p style="font-size: 1.2rem">Pimpinan Rapat :</p>
                                </span>
                                <button
                                    class="btn btn-secondary px-4 mt-1">{{ $agendaRapat->rapatAgendaPimpinan->nama }}</button>
                            </div>
                        </div>
                    </div>
                </x-adminlte-callout>
                <div class="daftar-peserta mt-5">
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
                <div class="daftar-peserta mt-5">
                    <h4>Catatan Rapat :</h4>
                    <hr>
                    <input id="x" type="hidden" value="{{ old('catatan_rapat') }}" name="catatan_rapat">
                    <trix-editor placeholder="Masukan Catatan Rapat Atau Bisa Unggah File" input="x"></trix-editor>
                </div>
                <div class="notulen-upload mt-5">
                    <hr>
                    <x-adminlte-card title="Unggah File Notulen ( Jika Ada ) :" theme="primary">
                        <div class="form-group">
                            <input type="file" class="form-control-file" name="notulen_file[]" multiple
                                id="exampleFormControlFile1">
                        </div>
                    </x-adminlte-card>
                </div>
                <div class="dokumentasi-upload mt-5">
                    <x-adminlte-card title="Unggah Dokumentasi Rapat :" theme="primary">
                        <div class="form-group">
                            <input type="file" name="dokumentasi_file[]" class="form-control-file" multiple
                                id="exampleFormControlFile1">
                        </div>
                    </x-adminlte-card>
                </div>
                <div class="text-center mx-auto">
                    <button class="btn btn-primary mt-3">Simpan</button>
                </div>
            </form>
        </div>
    </x-adminlte-card>
@endsection

@push('js')
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
@endpush
