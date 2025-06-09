@extends('adminlte::page')
@section('title', 'Rapat')
@section('plugins.Select2', true)
@section('content_header')
    <h1 class="m-0 text-dark"></h1>
@stop

@push('css')
@endpush

@section('content')
    <x-adminlte-card>
        <div class="d-flex justify-content-center">
            <form class="col-lg-9 col-md-12 col-sm-12" id="form-update-rapat" enctype="multipart/form-data">
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
                <div class="row">
                    <div class="col-lg-7 col-md-12 col-sm-12">
                        <div class="mb-3">
                            <label>Pilih Kepanitiaan : ( Jika Rapat Merupakan Rapat Kepanitiaan )</label>
                            <div class="invalid-feedback" id="error-kepanitiaan_id"></div>
                            <select class="form-control select-kepanitiaan" id="kepanitiaan" name="kepanitiaan_id">
                                <option value="">-- Pilih Kepanitiaan --</option>
                                @foreach ($kepanitiaans as $kepanitiaan)
                                    <option value="{{ $kepanitiaan->id }}"
                                        {{ $rapatAgenda->kepanitiaan_id == $kepanitiaan->id ? 'selected' : '' }}>
                                        {{ $kepanitiaan->nama_kepanitiaan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-3 col-sm-3">
                        @if ($rapatAgenda->rapatKepanitiaan !== null)
                            <div class="mt-2 table-anggota-panitia-group col-lg-12 col-md-12 col-sm-12"
                                id="table-anggota-panitia-before">
                                <label>Daftar Anggota Kepanitiaan Yang Akan Diundang : <x-adminlte-button
                                        label="Lihat Anggota Panitia" data-toggle="modal"
                                        data-target="#anggotaPanitiaModalBefore" class="bg-info" /> </label>
                                <x-adminlte-modal id="anggotaPanitiaModalBefore" title="Daftar Anggota Kepanitiaan"
                                    theme="info" icon="fas fa-users" size='lg' v-centered static-backdrop scrollable>
                                    <table class="table table-hover w-100">
                                        <caption>Daftar Pegawai Yang Akan Di Undang</caption>
                                        <thead class="thead-dark">
                                            <tr>
                                                <th scope="col">No</th>
                                                <th scope="col">Nama Peserta</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rapatAgenda->rapatKepanitiaan->pegawai as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->formatted_name }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </x-adminlte-modal>
                            </div>
                        @endif
                        <div class="mt-2 table-anggota-panitia-group col-lg-12 col-md-12 col-sm-12" style="display: none;">
                            <label>Daftar Anggota Kepanitiaan Yang Akan Diundang : <x-adminlte-button
                                    label="Lihat Anggota Panitia" data-toggle="modal"
                                    data-target="#anggotaPanitiaModalAfter" class="bg-info" /> </label>
                            <x-adminlte-modal id="anggotaPanitiaModalAfter" title="Daftar Anggota Kepanitiaan"
                                theme="info" icon="fas fa-users" size='lg' v-centered static-backdrop scrollable>
                                <table class="table table-hover w-100" id="table-anggota-panitia">
                                    <caption>Daftar Pegawai Yang Akan Di Undang</caption>
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col">No</th>
                                            <th scope="col">Nama Peserta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </x-adminlte-modal>
                        </div>
                    </div>
                </div>
                <div class="my-4" id="peserta-rapat">
                    <div class="d-flex justify-content-between mb-4">
                        <label
                            id="peserta-rapat-label">{{ $rapatAgenda->rapatKepanitiaan != null ? 'Pilih Pegawai Diluar Kepanitiaan ( Jika Ada ) :' : 'Pilih Peserta Rapat :' }}</label>
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
    @include('rapat::js.rapat.variable-js')
    @include('rapat::js.kepanitiaan.tampilkanAnggotaPanitia-js')

    @include('rapat::js.rapat.pesertaRapatTable-js')
    @include('rapat::js.rapat.pimpinanRapatTable-js')
    @include('rapat::js.rapat.notulisRapatTable-js')
    @include('rapat::js.rapat.kepanitiaanRapat-js')
    @include('rapat::js.rapat.editRapat-js')
    <script>
        const slug = "{{ $slug }}";
        const rapat = <?php echo json_encode($rapatAgenda); ?>;
    </script>

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
