@extends('adminlte::page')
@section('title', 'Rapat')
{{-- @section('plugins.Select2', true) --}}
@section('content_header')
    <h1 class="m-0 text-dark"></h1>
@stop

@push('css')
@endpush

@section('content')
    @php
        $heads = [
            ['label' => 'ID', 'width' => 5, 'class' => 'text-center'],
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
        <h4 class="text-center mb-4">{{ $rapat->agenda_rapat }}</h4>
        <div class="col-lg-9 mx-auto mt-5">
            <x-adminlte-datatable id="table1" :heads="$heads">
                @foreach ($config['data'] as $row)
                    <tr>
                        @foreach ($row as $cell)
                            <td>{!! $cell !!}</td>
                        @endforeach
                    </tr>
                @endforeach
            </x-adminlte-datatable>
        </div>

    </x-adminlte-card>
@endsection

@push('js')
    <script></script>
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
