@extends('adminlte::page')
@section('title', 'Edit Tugas')
{{-- @section('plugins.Select2', true) --}}
@section('content_header')
    <h5 class="m-0 text-dark">Edit Tugas</h5>
@stop

@push('css')
@endpush

@section('content')
    <x-adminlte-card>
        @php
            \Carbon\Carbon::setLocale('id');
        @endphp
        <div class="col-8 mx-auto mt-4">
            <div class="row">
                <div class="col-lg-6 col-sm-12 col-md-12">
                    <p><strong>Deskripsi Tugas :</strong></p>
                    <p> {{ $rapatTindakLanjut->deskripsi_tugas }}</p>
                </div>
                <div class="col-lg-6 col-sm-12 col-md-12">
                    <p><strong>Target Penyelesaian :</strong></p>
                    <p> {{ \Carbon\Carbon::parse($rapatTindakLanjut->batas_waktu)->translatedFormat('l, d F Y') }}</p>
                </div>
            </div>
            <form action="{{ url('/rapat/tindak-lanjut-rapat/tugas/' . $rapatTindakLanjut->slug . '/ubah-tugas') }}"
                method="POST" enctype="multipart/form-data" class="mt-4">
                @method('PUT')
                @csrf
                <div class="row">
                    <div class="col-lg-6 col-sm-12 col-md-12">
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Tugas (Opsional):</label>
                            <input type="text" name="tugas" class="form-control @error('tugas') is-invalid @enderror"
                                value="{{ $rapatTindakLanjut->tugas }}">
                            @error('tugas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12 col-md-12">
                        <div class="mb-3">
                            <label for="exampleFormControlTextarea1" class="form-label">Kendala (Jika Ada):</label>
                            <textarea class="form-control @error('kendala') is-invalid @enderror" name="kendala" id="exampleFormControlTextarea1"
                                rows="3">{{ $rapatTindakLanjut->kendala }}</textarea>
                            @error('kendala')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="formFile" class="form-label">File Tugas (Jika Ada):</label>
                    <input class="form-control @error('file_tugas') is-invalid @enderror" name="file_tugas[]" multiple
                        type="file" id="formFile">
                    @foreach ($errors->get('file_tugas.*') as $fileErrors)
                        @foreach ($fileErrors as $error)
                            <div class="invalid-feedback d-block">{{ $error }}</div>
                        @endforeach
                    @endforeach
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-warning">Edit Tugas</button>
                </div>
            </form>

        </div>
    </x-adminlte-card>
@endsection

@push('js')
@endpush
