@extends('adminlte::page')
@section('title', 'Tugaskan Peserta Rapat')
{{-- @section('plugins.Select2', true) --}}
@section('content_header')
    <h5 class="m-0 text-dark">Tugaskan Peserta Rapat</h5>
@stop

@push('css')
@endpush

@section('content')
    <x-adminlte-card>
        <!-- Header Section -->
        <div class="card-header bg-gradient-primary text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-user-plus mr-2"></i>
                        Form Penugasan Peserta
                    </h4>
                </div>
                <div class="col-auto">
                    <span class="badge badge-light px-3 py-2">
                        <i class="fas fa-clipboard-list mr-1"></i>
                    </span>
                </div>
            </div>
        </div>

        <!-- Body Section -->
        <div class="card-body px-4 py-5">
            <!-- Agenda Rapat Section -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="text-center">
                        <h6 class="text-muted mb-2 text-uppercase font-weight-bold">Agenda Rapat</h6>
                        <h4 class="font-weight-bold text-dark border-bottom border-info pb-3 d-inline-block px-4">
                            {{ $rapat->agenda_rapat }}
                        </h4>
                    </div>
                </div>
            </div>

            <!-- Participant Info Section -->
            <div class="row justify-content-center mb-5">
                <div class="col-lg-10 col-sm-12 col-md-10">
                    <div class="card border-left-primary shadow-sm">
                        <div class="card-body bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-3 col-sm-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle text-primary fa-2x mr-3"></i>
                                        <div>
                                            <h6 class="mb-0 font-weight-bold text-secondary">Peserta Rapat</h6>
                                            <small class="text-muted">Nama Lengkap</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9 col-sm-8 mt-3 mt-sm-0">
                                    <div class="bg-white rounded px-3 py-2 border">
                                        <h5 class="mb-0 font-weight-bold text-dark">{{ $peserta->formatted_name }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignment Form Section -->
            <div class="row justify-content-center">
                <div class="col-lg-10 col-sm-12 col-md-10">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 font-weight-bold text-secondary">
                                <i class="fas fa-edit mr-2"></i>
                                Detail Penugasan
                            </h6>
                        </div>

                        <div class="card-body">
                            <form
                                action="{{ url('/rapat/agenda-rapat/' . $rapat->slug . '/tugaskan/' . $peserta->username) }}"
                                method="POST">
                                @csrf

                                <!-- Task Description -->
                                <div class="form-group mb-4">
                                    <label for="deskripsi-tugas" class="form-label font-weight-bold text-secondary">
                                        <i class="fas fa-tasks mr-2 text-info"></i>
                                        Deskripsi Tugas
                                    </label>
                                    <textarea class="form-control form-control-lg @error('deskripsi') is-invalid @enderror" name="deskripsi"
                                        id="deskripsi-tugas" rows="4" placeholder="Masukkan deskripsi tugas yang akan diberikan kepada peserta...">{{ old('deskripsi') }}</textarea>
                                    @error('deskripsi')
                                        <div class="invalid-feedback d-flex align-items-center">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Jelaskan tugas secara detail dan spesifik
                                    </small>
                                </div>

                                <!-- Deadline -->
                                <div class="form-group mb-4">
                                    <label for="deskripsi-date" class="form-label font-weight-bold text-secondary">
                                        <i class="fas fa-calendar-alt mr-2 text-warning"></i>
                                        Target Penyelesaian
                                    </label>
                                    <input type="date" name="batas_waktu"
                                        class="form-control form-control-lg @error('batas_waktu') is-invalid @enderror"
                                        id="deskripsi-date" value="{{ old('batas_waktu') }}" min="{{ date('Y-m-d') }}">
                                    @error('batas_waktu')
                                        <div class="invalid-feedback d-flex align-items-center">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pilih tanggal target penyelesaian tugas
                                    </small>
                                </div>

                                <!-- Action Buttons -->
                                <div class="form-group mb-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <button type="submit" class="btn btn-primary shadow">
                                                <i class="fas fa-paper-plane mr-2"></i>
                                                Tugaskan Sekarang
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-adminlte-card>
@endsection

@push('js')
@endpush
