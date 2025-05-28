@extends('adminlte::page')
@section('title', 'Rapat')
{{-- @section('plugins.Select2', true) --}}
@section('content_header')
    <h3 class="m-0 text-dark">Buat Agenda Rapat</h3>
@stop

@push('css')
@endpush

@section('content')

    <x-adminlte-card>
        <div class="d-flex justify-content-center">
            <form method="POST" class="col-lg-8 col-md-12 col-sm-12" id="form-agenda-rapat" enctype="multipart/form-data">
                <div id="form-errors" class="alert alert-danger d-none">
                    <ul id="form-errors-list" class="mb-0"></ul>
                </div>
                @csrf
                <div class="mb-3">
                    <label for="nomor-surat" class="form-label">Nomor Surat Undangan:</label>
                    <input type="text" name="nomor_surat" class="form-control" id="nomor-surat">
                    <div class="invalid-feedback" id="error-nomor_surat"></div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <label for="waktu-mulai" class="form-label">Waktu Mulai :</label>
                            <input type="datetime-local" name="waktu_mulai" class="form-control" id="waktu-mulai">
                            <div class="invalid-feedback" id="error-waktu_mulai"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <label for="waktu-selesai" class="form-label">Waktu Selesai :</label>
                            <select id="pilihan-waktu-selesai" class="form-control mb-2">
                                <option value="">-- Pilih --</option>
                                <option value="manual">Masukkan Tanggal</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>
                        <input type="datetime-local" name="waktu_selesai" class="form-control" id="waktu-selesai">
                        <div class="invalid-feedback" id="error-waktu_selesai"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="tempat" class="form-label">Tempat Rapat</label>
                    <div class="invalid-feedback" id="error-tempat"></div>
                    <select id="pilihan-tempat" class="form-control ">
                        <option selected value="">-- Pilih Tempat --</option>
                        <option value="zoom">Online</option>
                        <option value="custom">Tempat Lain</option>
                    </select>
                    <div class="mt-3" id="tempat-rapat-group">
                        <label for="tempat-rapat" class="form-label">Masukkan Tempat Rapat</label>
                        <input type="text" id="tempat-rapat" name="tempat_rapat" class="form-control "
                            placeholder="Masukkan Tempat Rapat">
                    </div>
                </div>
                <div class="mb-3">
                    <label>Agenda Rapat :</label>
                    <textarea class="form-control" id="agenda-rapat" name="agenda_rapat" placeholder="Agenda Rapat"></textarea>
                    <div class="invalid-feedback" id="error-agenda_rapat"></div>
                </div>
                <div class="mb-3">
                    <label>Pilih Kepanitiaan : ( Jika Rapat Merupakan Rapat Kepanitiaan )</label>
                    <div class="invalid-feedback" id="error-kepanitiaan_id"></div>
                    <select class="form-control" id="kepanitiaan" name="kepanitiaan_id">
                        <option value="">-- Pilih Kepanitiaan --</option>
                        @foreach ($kepanitiaans as $kepanitiaan)
                            <option value="{{ $kepanitiaan->id }}">{{ $kepanitiaan->nama_kepanitiaan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 my-4">
                    <div class="my-4" id="peserta-rapat">
                        <div class="d-flex justify-content-between mb-4">
                            <label>Pilih Peserta Rapat :</label>
                        </div>
                        <div class="invalid-feedback" id="error-peserta_rapat"></div>
                        <table id="table-peserta-rapat" class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama Peserta</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Pilih</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3">
                        <label>Lampiran : ( Jika Ada )</label>
                        <input type="file" name="lampiran[]" class="form-control" id="lampiran-file" multiple>
                        @error('lampiran.*')
                            <span class="text-danger d-block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="my-4">
                        <label>Pilih Pimpinan Rapat :</label>
                        <div class="invalid-feedback" id="error-pimpinan_username"></div>
                        <table id="table-pimpinan-rapat" class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama Peserta</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Undang</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3 my-4">
                        <label>Pilih Notulis Rapat :</label>
                        <table id="table-notulis-rapat" class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama Peserta</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Undang</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                        <div class="invalid-feedback" id="error-notulis_username"></div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary mx-auto">Submit</button>
                    </div>
            </form>
        </div>
    </x-adminlte-card>
@endsection

@push('js')
    <script src="{{ asset('assets/js/rapat/variable.js') }}"></script>
    <script src="{{ asset('assets/js/rapat/pesertaRapatTable.js') }}"></script>
    <script src="{{ asset('assets/js/rapat/pimpinanRapatTable.js') }}"></script>
    <script src="{{ asset('assets/js/rapat/notulisRapatTable.js') }}"></script>
    <script src="{{ asset('assets/js/rapat/kepanitiaanRapat.js') }}"></script>
    <script src="{{ asset('assets/js/rapat/createRapat.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            window.addEventListener("swal", (event) => {
                Swal.fire({
                    title: event.detail.title,
                    text: event.detail.text,
                    icon: event.detail.icon
                });
            });
        });
    </script>
@endpush
