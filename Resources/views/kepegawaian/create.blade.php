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

            <form id="formKepanitiaan" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label>Nama Kepanitiaan</label>
                    <input type="text" id="nama_kepanitiaan" name="nama_kepanitiaan" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <label>Tanggal Mulai</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Tanggal Berakhir</label>
                    <input type="date" id="tanggal_berakhir" name="tanggal_berakhir" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Tujuan</label>
                    <input type="text" id="tujuan" name="tujuan" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Peserta Kepanitiaan :</label>
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

                <div class="mb-3 mt-4">
                    <label>Struktur Kepanitiaan :</label>
                    <table id="table-struktur-kepanitiaan" class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col" width="5%">No</th>
                                <th scope="col" width="35%">Nama Peserta</th>
                                {{-- <th scope="col">Email</th> --}}
                                <th scope="col">Jabatan</th>
                                <th scope="col" width="15%" title="Pilih satu anggota sebagai ketua panitia">Pilih
                                    Ketua Panitia</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </x-adminlte-card>
@endsection

@push('js')
    <script src="{{ asset('assets/js/rapat/variable.js') }}"></script>
    <script src="{{ asset('assets/js/rapat/pesertaRapatTable.js') }}"></script>
    <script src="{{ asset('assets/js/panitia/strukturKepanitiaanTable.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $('#formKepanitiaan').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            pesertaRapat.forEach(p => formData.append('peserta_panitia[]', p));
            formData.append('pimpinan_username', pimpinanKepanitiaan);
            //mengambil data struktur kepanitiaan, yang inputan nya berada di datatable pada form group struktur kepanitiaan
            const inputs = document.querySelectorAll('.jabatan-input');
            const strukturKepanitiaan = [];

            inputs.forEach(input => {
                const jabatan = input.value.trim();
                const username = input.dataset.id;
                if (username == pimpinanKepanitiaan) {
                    return;
                }
                strukturKepanitiaan.push({
                    jabatan: jabatan == "" ? 'Anggota' : jabatan,
                    username: username
                });
            });
            formData.append('struktur_kepanitiaan', JSON.stringify(strukturKepanitiaan));
            $.ajax({
                url: '/rapat/panitia',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#error-alert').addClass('d-none');
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
