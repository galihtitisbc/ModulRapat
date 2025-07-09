@extends('adminlte::page')
@section('title', 'Rapat')
{{-- @section('plugins.Select2', true) --}}
@section('content_header')
    <h3 class="m-0 text-dark">Riwayat Rapat</h3>
@stop

@section('content')
    @php
        use Carbon\Carbon;
        Carbon::setLocale('id');
    @endphp
    <div class="row">
        <div class="col-12 ">
            <x-adminlte-card>
                <form action="" method="get">
                    <div class="col-lg-9 col-sm-12 mx-auto my-3">
                        <div class="row">
                            <div class="col-lg-5 col-sm-12 mb-2">
                                <input type="text" name="cari" class="form-control" placeholder="Cari Agenda Rapat">
                            </div>
                            <div class="col-lg-6 col-sm-12 mb-2">
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12 mb-2">
                                        <input type="text" onfocus="(this.type='date')" onblur="(this.type='text')"
                                            name="dari_tgl" class="form-control mb-2" value="{{ request('dari_tgl') }}"
                                            placeholder="Rapat Dari Tanggal">
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mb-2">
                                        <input type="text" onfocus="(this.type='date')" onblur="(this.type='text')"
                                            name="sampai_tgl" class="form-control" value="{{ request('sampai_tgl') }}"
                                            placeholder="Rapat Sampai Tanggal">
                                    </div>
                                    <div class="col-lg-12 col-sm-12 mb-2">
                                        <select class="form-control" id="filterRapat" name="filter"
                                            onchange="this.form.submit()">
                                            <option value="">-- Pilih Filter --</option>
                                            <option value="minggu" {{ request('filter') == 'minggu' ? 'selected' : '' }}>
                                                Rapat Minggu Ini</option>
                                            <option value="bulan" {{ request('filter') == 'bulan' ? 'selected' : '' }}>
                                                Rapat Bulan Ini</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1 col-md-12 col-sm-12">
                                <button class="btn btn-primary col-sm-12"><i class="fas fa-search"></i></button>
                                @if (request('cari') || request('dari_tgl') || request('sampai_tgl') || request('filter'))
                                    <button type="button"
                                        onclick="this.form.reset(); window.location='{{ url('rapat/riwayat-rapat') }}'"
                                        class="btn btn-danger mt-2 col-sm-12">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col" width="5%">No</th>
                            <th scope="col" width="50%">Agenda Rapat</th>
                            <th scope="col" width="20%">Waktu Mulai</th>
                            <th scope="col" class="text-center">Detail</th>
                            <th scope="col" class="text-center">Laporan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rapats as $rapat)
                            <tr>
                                <th scope="row">
                                    {{ ($rapats->currentPage() - 1) * $rapats->perPage() + $loop->iteration }}
                                </th>
                                <td>
                                    @if ($rapat->pimpinan_id == Auth::user()->pegawai->id)
                                        <span class="badge bg-success mb-1">Pimpinan Rapat</span><br>
                                    @elseif ($rapat->notulis_id == Auth::user()->pegawai->id)
                                        <span class="badge bg-primary mb-1">Notulis Rapat</span><br>
                                    @endif
                                    {{ $rapat->agenda_rapat }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($rapat->waktu_mulai)->translatedFormat('l, j F Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ url('rapat/agenda-rapat/' . $rapat->slug . '/detail') }}">
                                        <i class="fas fa-eye fa-lg" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Detail Rapat"></i>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a target="_blank"
                                        href="{{ url('/rapat/riwayat-rapat/' . $rapat->slug . '/generate-pdf') }}">
                                        <i class="fas fa-download fa-lg"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak Ada Riwayat Rapat</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-2">
                    {{ $rapats->links() }}
                </div>
            </x-adminlte-card>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });
    </script>
@endpush
