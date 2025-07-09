@extends('adminlte::page')
@section('title', 'Tindak Lanjut Rapat')
{{-- @section('plugins.Select2', true) --}}
@section('content_header')
    <h5 class="m-0 text-dark">Tindak Lanjut Rapat</h5>
@stop

@push('css')
@endpush

@section('content')
    @php
        use Modules\Rapat\Http\Helper\StatusTindakLanjut;
        use Modules\Rapat\Http\Helper\RoleGroupHelper;
    @endphp
    <div class="row">
        <div class="col-12">
            @if ($tindakLanjutRapat->isEmpty())
                <x-adminlte-card>
                    <div class="col-6 mx-auto mt-5">
                        <x-adminlte-alert theme="success">
                            Anda Tidak Memiliki Tugas
                        </x-adminlte-alert>
                    </div>
                </x-adminlte-card>
            @endif
            @if ($tindakLanjutRapat->isNotEmpty())
                <x-adminlte-card>
                    <form action="" method="get">
                        <div class="col-lg-9 col-sm-12 mx-auto my-3">
                            <div class="row">
                                <div class="col-lg-5 col-sm-12 mb-2">
                                    <input type="text" name="cari" class="form-control"
                                        placeholder="Cari Agenda Rapat">
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
                                            <select class="form-control" id="filterRapat" name="status"
                                                onchange="this.form.submit()">
                                                <option value="">-- Pilih Status Penyelesaian --</option>
                                                <option value="SELESAI"
                                                    {{ request('status') == 'SELESAI' ? 'selected' : '' }}>
                                                    Selesai</option>
                                                <option value="BELUM_SELESAI"
                                                    {{ request('status') == 'BELUM_SELESAI' ? 'selected' : '' }}>
                                                    Belum Selesai</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-12 col-sm-12">
                                    <button class="btn btn-primary col-sm-12"><i class="fas fa-search"></i></button>
                                    @if (request('cari') || request('dari_tgl') || request('sampai_tgl') || request('status'))
                                        <button type="button"
                                            onclick="this.form.reset(); window.location='{{ url('rapat/tindak-lanjut-rapat') }}'"
                                            class="btn btn-danger mt-2 col-sm-12">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                    <table class="table">
                        <thead>
                            <tr class="text-center">
                                <th style="width: 5%;">No</th>
                                <th style="width: 30%;" class="text-left">Agenda Rapat</th>
                                <th style="width: 20%;">Tanggal Rapat</th>
                                <th style="width: 10%;">Penyelesaian Penugasan</th>
                                <th style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $statusTindakLanjut = [
                                    'SELESAI' => 'success',
                                    'BELUM_SELESAI' => 'danger',
                                ];
                                $no = 1;
                            @endphp

                            @forelse ($tindakLanjutRapat as $index => $tindakLanjut)
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    <td>
                                        @if ($tindakLanjut->rapatAgenda->pimpinan_id == Auth::user()->pegawai->id)
                                            <span class="badge bg-success mb-1">Pimpinan Rapat</span><br>
                                        @elseif ($tindakLanjut->rapatAgenda->notulis_id == Auth::user()->pegawai->id)
                                            <span class="badge bg-primary mb-1">Notulis Rapat</span><br>
                                        @endif
                                        {{ $tindakLanjut->rapatAgenda->agenda_rapat }}
                                    </td>

                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($tindakLanjut->rapatAgenda->waktu_mulai)->locale('id')->translatedFormat('l, d F Y') }}
                                    </td>
                                    <td class="text-center">
                                        @if (
                                            $tindakLanjut->rapatAgenda->pimpinan_id == Auth::user()->pegawai->id ||
                                                $tindakLanjut->rapatAgenda->notulis_id == Auth::user()->pegawai->id ||
                                                RoleGroupHelper::userHasRoleGroup(Auth::user(), RoleGroupHelper::pimpinanRoles()))
                                            {{-- Jika user adalah pimpinan, notulis, atau memiliki role pimpinan, tampilkan persentase penyelesaian --}}
                                            {{ $tindakLanjut->rapatAgenda->status_persentase_penyelesaian }}%
                                        @else
                                            {{-- Jika bukan, tampilkan status tugas pribadi (pegawai_id) --}}
                                            @php
                                                // Cari status tugas user saat ini pada agenda yang sama
                                                $userTugas = $tindakLanjut->rapatAgenda->rapatTindakLanjut->firstWhere(
                                                    'pegawai_id',
                                                    Auth::user()->pegawai->id,
                                                );
                                            @endphp

                                            @if ($userTugas)
                                                <span class="badge badge-{{ $statusTindakLanjut[$userTugas->status] }}">
                                                    {{ StatusTindakLanjut::from($userTugas->status)->label() }}
                                                </span>
                                            @else
                                                <span class="text-muted">Tidak ada tugas</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ url('rapat/tindak-lanjut-rapat/' . $tindakLanjut->rapatAgenda->slug . '/detail') }}"
                                            class="btn btn-secondary">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mx-auto">
                        {{ $tindakLanjutRapat->links() }}
                    </div>
                </x-adminlte-card>
            @endif
        </div>
    </div>
@endsection

@push('js')
    <script></script>
@endpush
