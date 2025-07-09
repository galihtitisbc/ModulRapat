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
        @endphp
        <div class="col-sm-12 col-lg-10 mx-auto">
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
                enctype="multipart/form-data" id="form-notulen">
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
                    <div class="check-all d-flex justify-content-between">
                        <h4>Daftar Peserta :</h4>
                        <hr>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-all-peserta">
                            <label class="form-check-label" for="defaultCheck1">
                                <b> Semua Peserta Hadir</b>
                            </label>
                        </div>
                    </div>
                    <table id="table-daftar-peserta" class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Peserta</th>
                                <th>Email</th>
                                <th>Status Konfirmasi</th>
                                <th>
                                    Hadir
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($agendaRapat->rapatAgendaPeserta as $key => $rapat)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td>{{ $rapat->formatted_name }}</td>
                                    <td>{{ $rapat->user->email }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $statusKehadiran[$rapat->pivot->status] }}">
                                            {{ StatusPesertaRapat::from($rapat->pivot->status)->label() }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <input class="form-check-input peserta-checkbox" type="checkbox"
                                            name="peserta_hadir[]" value="{{ $rapat->id }}"
                                            {{ in_array($rapat->id, old('peserta_hadir', [])) ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

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
    <script>
        document.getElementById('check-all-peserta').addEventListener('change', function() {
            const isChecked = this.checked;
            const checkboxes = document.querySelectorAll('input[name="peserta_hadir[]"]');
            checkboxes.forEach(cb => cb.checked = isChecked);
        });
        let table = $('#table-daftar-peserta').DataTable({
            paging: false,
            order: [
                [0, 'asc']
            ],
            columnDefs: [{
                    targets: 0,
                    className: 'text-center'
                },
                {
                    targets: 3,
                    className: 'text-center'
                },
                {
                    targets: 4,
                    className: 'text-center',
                    orderable: false
                },
                {
                    targets: 2,
                    orderable: false
                }
            ]
        });
        $('#check-all-peserta').on('change', function() {
            const isChecked = this.checked;
            table.rows().every(function() {
                const row = this.node();
                const checkbox = $(row).find('input.peserta-checkbox');
                checkbox.prop('checked', isChecked);
            });
        });
    </script>
@endpush
