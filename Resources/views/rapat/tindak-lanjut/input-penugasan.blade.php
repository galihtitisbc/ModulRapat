@extends('adminlte::page')
@section('title', 'Input Penugasan')
{{-- @section('plugins.Select2', true) --}}
@section('content_header')
    <h3 class="m-0 text-dark">Input Penugasan</h3>
@stop

@push('css')
@endpush

@section('content')
    @php
        $heads = [
            ['label' => 'No', 'width' => 5, 'class' => 'text-center'],
            ['label' => 'Nama', 'width' => 30, 'class' => 'text-center'],
            ['label' => 'Aksi', 'width' => 20, 'class' => 'text-center'],
        ];
        $spanLihatTugas =
            '<span><i class="fas fa-eye fa-lg" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Tugas"></i></span>';
        $config = [
            'data' => $data,
            'order' => [[1, 'asc']],
        ];
    @endphp
    <x-adminlte-card>
        <!-- Header Section -->
        <div class="card-header bg-gradient-warning text-dark">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-tasks mr-2"></i>
                        Input Penugasan Rapat
                    </h4>
                </div>
                <div class="col-auto">
                    <span class="badge badge-light px-3 py-2">
                        <i class="fas fa-calendar mr-1"></i>
                        Form Input
                    </span>
                </div>
            </div>
        </div>

        <!-- Body Section -->
        <div class="card-body px-4 py-5">
            <!-- Agenda Rapat Title -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="text-center">
                        <h5 class="text-primary mb-2">Agenda Rapat:</h5>
                        <h4 class="font-weight-bold text-dark border-bottom border-primary pb-2 d-inline-block">
                            {{ $rapat->agenda_rapat }}
                        </h4>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12 col-sm-12 col-xl-10">

                    <!-- No Assignment Option -->
                    <div class="card border-warning mb-4">
                        <div class="card-body bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle text-warning fa-2x mr-3"></i>
                                        <div>
                                            <h6 class="mb-1 font-weight-bold">Rapat Tanpa Penugasan</h6>
                                            <p class="mb-0 text-muted">Klik tombol Sebelah Kanan jika rapat tidak memiliki
                                                penugasan tindak lanjut</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-right mt-3 mt-md-0">
                                    <form
                                        action="{{ url('/rapat/tindak-lanjut-rapat/' . $rapat->slug . '/tidak-ada-tugas') }}"
                                        method="post" id="rapat-tidak-ada-tugas">
                                        @csrf
                                        <button class="btn btn-warning shadow-sm">
                                            <i class="fas fa-times-circle mr-2"></i>
                                            Tidak Ada Penugasan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assignment Input Section -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <x-adminlte-datatable id="table1" :heads="$heads"
                                    class="table-striped table-hover mb-0">
                                    @foreach ($config['data'] as $row)
                                        <tr>
                                            @foreach ($row as $cell)
                                                <td class="py-3 px-4 align-middle">{!! $cell !!}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </x-adminlte-datatable>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ url('/rapat/agenda-rapat') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-adminlte-card>
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
    <script>
        let isConfirmed = false;
        $('#rapat-tidak-ada-tugas').submit(function(event) {
            if (!isConfirmed) {
                event.preventDefault();
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Rapat Tidak Memiliki Penugasan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Tidak Ada Penugasan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        isConfirmed = true;
                        $('#rapat-tidak-ada-tugas').submit();
                    }
                });
            }
        })
    </script>
@endpush
