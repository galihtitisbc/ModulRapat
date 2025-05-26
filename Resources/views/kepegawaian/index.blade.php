@extends('adminlte::page')
@section('title', 'Kepanitiaan')
{{-- @section('plugins.Select2', true) --}}
@section('content_header')
    <h3 class="m-0 text-dark">Kepanitiaan</h3>
@stop

@push('css')
@endpush

@section('content')
    @php
        use Modules\Rapat\Http\Helper\RoleGroupHelper;
        $heads = [
            'ID',
            'Nama Kepanitiaan',
            'Tanggal Mulai',
            'Tanggal Berakhir',
            'Status',
            ['label' => 'Actions', 'no-export' => true, 'width' => 15],
        ];
    @endphp
    <x-adminlte-card>
        @if (RoleGroupHelper::userHasRoleGroup(Auth::user(), RoleGroupHelper::kepegawaianRoles()))
            <div class="btn-tambah d-flex justify-content-end my-2">
                <a href="{{ url('rapat/panitia/create') }}" class="btn btn-primary">Tambah Kepanitiaan</a>
            </div>
        @endif
        <form action="" method="get">
            <div class="col-lg-9 col-sm-12 mx-auto my-3">
                <div class="row d-flex justify-content-center">
                    <div class="col-lg-5 col-md-12 col-sm-12 mb-2">
                        <input type="text" name="nama_kepanitiaan" class="form-control mb-2"
                            placeholder="Cari Kepanitiaan" value="{{ request('nama_kepanitiaan') }}">
                        <select name="status" class="form-control" id="">
                            <option value="" selected>-- Pilih Status --</option>
                            <option value="AKTIF" {{ request('status') == 'AKTIF' ? 'selected' : '' }}>Aktif</option>
                            <option value="NON_AKTIF" {{ request('status') == 'NON_AKTIF' ? 'selected' : '' }}>Tidak Aktif
                            </option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-12 col-sm-12 mb-2">
                        <input type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="dari_tgl"
                            class="form-control mb-2" value="{{ request('dari_tgl') }}" placeholder="Aktif Dari Tanggal">
                        <input type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="sampai_tgl"
                            class="form-control" value="{{ request('sampai_tgl') }}" placeholder="Berakhir Sampai Tanggal">
                    </div>
                    <div class="col-lg-1 col-md-12 col-sm-12">
                        <button class="btn btn-primary col-sm-12"><i class="fas fa-search"></i></button>
                        @if (request('nama_kepanitiaan') || request('dari_tgl') || request('sampai_tgl') || request('status'))
                            <button type="button" onclick="this.form.reset(); window.location='{{ url('rapat/panitia') }}'"
                                class="btn btn-danger mt-2 col-sm-12">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <th>No</th>
                    <th>Nama Kepanitiaan</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Berakhir</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </thead>
                <tbody>
                    @foreach ($kepanitiaans as $kepanitiaan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $kepanitiaan->nama_kepanitiaan }}</td>
                            <td>{{ $kepanitiaan->tanggal_mulai }}</td>
                            <td>{{ $kepanitiaan->tanggal_berakhir }}</td>
                            <td>
                                <span class="badge bg-{{ $kepanitiaan->status == 'AKTIF' ? 'success' : 'danger' }}">
                                    {{ $kepanitiaan->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ url('/rapat/panitia/' . $kepanitiaan->slug . '/detail') }}"
                                    class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Detail Kepanitiaan"></i>
                                </a>
                                @hasrole('kepegawaian')
                                    <a href="{{ url('/rapat/panitia/' . $kepanitiaan->slug . '/edit') }}"
                                        class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fa fa-fw fa-pen"></i>
                                        <nobr>
                                    </a>
                                    <form action="{{ url('/rapat/panitia/' . $kepanitiaan->slug . '/change-status') }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-danger btn-sm" title="Ubah Status">
                                            <i class="fa fa-fw fa-exchange-alt"></i>
                                        </button>
                                    </form>
                                @endhasrole
                                </nobr>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-center">
                {{ $kepanitiaans->links() }}
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
@endpush
