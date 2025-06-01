@extends('adminlte::page')
@section('title', 'Detail Tindak Lanjut Rapat')
{{-- @section('plugins.Select2', true) --}}
@section('content_header')
    <h5 class="m-0 text-dark">Detail Tindak Lanjut</h5>
@stop

@push('css')
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            @php
                use Modules\Rapat\Http\Helper\KriteriaPenilaian;
                use Modules\Rapat\Http\Helper\StatusTindakLanjut;
                $belumSelesaiEnum = StatusTindakLanjut::BELUM_SELESAI->value;
                $selesaiEnum = StatusTindakLanjut::SELESAI->value;
                $statusTindakLanjut = [
                    'SELESAI' => 'success',
                    'BELUM_SELESAI' => 'danger',
                ];
                $statusPenilaian = [
                    'BELUM_DINILAI' => 'secondary',
                    'MELEBIHI_EKSPETASI' => 'success',
                    'SESUAI_EKSPETASI' => 'primary',
                    'TIDAK_SESUAI_EKSPETASI' => 'danger',
                ];
                $heads = [
                    ['label' => 'No', 'width' => 3, 'class' => 'text-center'],
                    ['label' => 'Nama Peserta', 'width' => 15],
                    ['label' => 'Tugas', 'width' => 10],
                    ['label' => 'Target Penyelesaian', 'width' => 10],
                    ['label' => 'Tanggal Selesai', 'width' => 10],
                    ['label' => 'Status', 'width' => 10, 'class' => 'text-center'],
                    ['label' => 'Status Penilaian', 'width' => 10, 'class' => 'text-center'],
                    ['label' => 'Aksi', 'width' => 10, 'class' => 'text-center'],
                ];
                $data = [];
                $status = '';
                $aksi = '';
                $penilaian = '';
                foreach ($tindakLanjuts as $key => $tindakLanjut) {
                    $status =
                        '<span class="badge bg-' .
                        $statusTindakLanjut[$tindakLanjut->status] .
                        '">' .
                        StatusTindakLanjut::from($tindakLanjut->status)->label() .
                        '</span>';
                    $penilaian =
                        '<span class="badge bg-' .
                        $statusPenilaian[$tindakLanjut->penilaian] .
                        '">' .
                        KriteriaPenilaian::from($tindakLanjut->penilaian)->label() .
                        '</span>';
                    $btnDetail =
                        '<a href="' .
                        url('/rapat/tindak-lanjut-rapat/' . $tindakLanjut->slug . '/detail/tugas') .
                        '" class="btn btn-success mx-2 btn-detail"> <i class="fas fa-eye" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="Detail Tugas"></i></a>';
                    $btnUpdate =
                        '<a href="' .
                        url('/rapat/tindak-lanjut-rapat/tugas/' . $tindakLanjut->slug . '/ubah-tugas') .
                        '" class="btn btn-warning"> <i class="fas fa-edit" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Ubah Tugas"></i></a>';
                    $aksi = $tindakLanjut->status == $selesaiEnum ? $btnDetail : '-';
                    if (
                        $tindakLanjut->status == $belumSelesaiEnum &&
                        $tindakLanjut->pegawai_username == Auth::user()->pegawai->username
                    ) {
                        $aksi =
                            '<a href="' .
                            url('/rapat/tindak-lanjut-rapat/tugas/' . $tindakLanjut->slug . '/unggah-tugas') .
                            '" class="btn btn-primary">Unggah Tugas</a>';
                    }
                    if (
                        $tindakLanjut->status == $belumSelesaiEnum &&
                        $tindakLanjut->rapatAgenda->pimpinan_username == Auth::user()->pegawai->username
                    ) {
                        $aksi = '-';
                    }
                    if (
                        $tindakLanjut->status == $selesaiEnum &&
                        $tindakLanjut->pegawai_username == Auth::user()->pegawai->username &&
                        $tindakLanjut->penilaian == KriteriaPenilaian::BELUM_DINILAI->value
                    ) {
                        $aksi .= $btnUpdate;
                    }
                    if ($tindakLanjut->rapatAgenda->notulis_username == Auth::user()->pegawai->username) {
                        $aksi = '-';
                    }
                    $data[] = [
                        $key + 1,
                        $tindakLanjut->pegawai->formatted_name,
                        $tindakLanjut->deskripsi_tugas,
                        $tindakLanjut->batas_waktu,
                        $tindakLanjut->tanggal_selesai ? $tindakLanjut->tanggal_selesai : '-',
                        $status,
                        $penilaian,
                        $aksi,
                    ];
                }
                $config = [
                    'data' => $data,
                    'columns' => [
                        ['className' => 'text-center'],
                        null,
                        ['orderable' => false],
                        null,
                        ['className' => 'text-center'],
                        ['className' => 'text-center'],
                        ['className' => 'text-center'],
                        ['className' => 'text-center', 'orderable' => false],
                    ],
                ];
            @endphp
            <x-adminlte-card>
                <div class="col-11 mx-auto mt-4">
                    <x-adminlte-datatable id="detail-tindak-lanjut" :heads="$heads" :config="$config">
                        @foreach ($config['data'] as $row)
                            <tr>
                                @foreach ($row as $cell)
                                    <td>{!! $cell !!}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                    <!-- Modal -->
                </div>
            </x-adminlte-card>
        </div>
    </div>
@endsection

@push('js')

    @if (session('swal'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "{{ session('swal.title') }}",
                    text: "{{ session('swal.text') }}",
                    icon: "{{ session('swal.icon') }}"
                });
            });
        </script>
    @endif
@endpush
