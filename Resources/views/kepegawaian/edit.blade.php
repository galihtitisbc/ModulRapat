@extends('adminlte::page')
@section('title', 'Kepanitiaan')
{{-- @section('plugins.Select2', true) --}}
@section('content_header')
    <h3 class="m-0 text-dark">Buat Kepanitiaan</h3>
@stop

@push('css')
@endpush

@section('content')

    <x-adminlte-card>
        <div class="col-lg-8 col-sm-12 col-md-12 mx-auto">
            <div class="alert alert-danger d-none" id="error-alert">
                <strong>Terjadi kesalahan:</strong>
                <ul id="error-list"></ul>
            </div>
            <form id="formKepanitiaan" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Nama Kepanitiaan</label>
                    <input type="text" name="nama_kepanitiaan" class="form-control"
                        value="{{ old('nama_kepanitiaan', $kepanitiaan->nama_kepanitiaan ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" required>{{ old('deskripsi', $kepanitiaan->deskripsi ?? '') }}</textarea>
                </div>

                <div class="mb-3">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control"
                        value="{{ old('tanggal_mulai', $kepanitiaan->tanggal_mulai ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label>Tanggal Berakhir</label>
                    <input type="date" name="tanggal_berakhir" class="form-control"
                        value="{{ old('tanggal_berakhir', $kepanitiaan->tanggal_berakhir ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label>Tujuan</label>
                    <input type="text" name="tujuan" class="form-control"
                        value="{{ old('tujuan', $kepanitiaan->tujuan ?? '') }}" required>
                </div>
                <div class="mb-3">
                    <label>Peserta Kepanitiaan :</label>
                    <table id="table-peserta-rapat" class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Peserta</th>
                                <th scope="col">Whatsapp</th>
                                <th scope="col">Pilih</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="mb-3">
                    <label>Ketua Kepanitiaan :</label>
                    <table id="table-pimpinan-rapat" class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Peserta</th>
                                <th scope="col">Whatsapp</th>
                                <th scope="col">Undang</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="mb-3">
                    <label>Pengarah Kepanitiaan :</label>
                    <input type="text" id="pengarah" value="{{ old('pengarah', $kepanitiaan->pengarah ?? '') }}"
                        name="pengarah" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Penanggung Jawab Kepanitiaan :</label>
                    <input type="text" id="penanggung_jawab" name="penanggung_jawab" class="form-control"
                        value="{{ old('penanggung_jawab', $kepanitiaan->penanggung_jawab ?? '') }}">
                </div>
                <div class="mb-3">
                    <label>Sekretaris Kepanitiaan :</label>
                    <input type="text" id="sekretaris" name="sekretaris" class="form-control"
                        value="{{ old('sekretaris', $kepanitiaan->sekretaris ?? '') }}">
                </div>
                <div class="mb-3">
                    <label>Koordinator Kepanitiaan :</label>
                    <input type="text" id="koordinator" name="koordinator" class="form-control"
                        value="{{ old('koordinator', $kepanitiaan->koordinator ?? '') }}">
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </x-adminlte-card>
@endsection

@push('js')
    <script src="{{ asset('assets/js/rapat/variable.js') }}"></script>
    <script src="{{ asset('assets/js/rapat/pesertaRapatTable.js') }}"></script>
    <script src="{{ asset('assets/js/rapat/pimpinanRapatTable.js') }}"></script>
    <script>
        //mendapatkan data pegawai dari kepanitiaan yang dikirim controller, dan menambahkan ke array pesertaRapat sebagai anggota panitia
        const kepanitiaan = <?php echo json_encode($kepanitiaan); ?>;
        pimpinanRapatUsername = kepanitiaan.pimpinan_username;
        pesertaManual = kepanitiaan.pegawai.map((pegawai) => pegawai.username);
        pesertaRapat = [...new Set([...pesertaManual, ...pesertaKepanitiaan])];
        tablePimpinanRapat.ajax.reload();
        //-------------------------------

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $('#formKepanitiaan').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            pesertaRapat.forEach(p => formData.append('peserta_panitia[]', p));
            formData.append('pimpinan_username', pimpinanRapatUsername);
            $.ajax({
                url: '/rapat/panitia/' + kepanitiaan.slug,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-HTTP-Method-Override": "PUT",
                },
                success: function(response) {
                    console.log(response);
                    Swal.fire({
                        title: 'Berhasil',
                        text: `${response.message}`,
                        icon: 'success',
                    });
                    setTimeout(() => {
                        window.location.href = '/rapat/panitia';
                    }, 1500);
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorList = '';
                        Object.values(errors).forEach(messages => {
                            messages.forEach(message => {
                                errorList += `<li>${message}</li>`;
                            });
                        });
                        $('#error-list').html(errorList);
                        $('#error-alert').removeClass('d-none');
                        $('html, body').animate({
                            scrollTop: $('#error-alert').offset().top - 20
                        }, 500);
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Gagal Menambahkan Kepanitiaan',
                            icon: 'error',
                        });
                    }
                }
            });
        });
    </script>
@endpush
