@extends('adminlte::page')
@section('title', 'Unggah Tugas')
{{-- @section('plugins.Select2', true) --}}
@section('content_header')
    <h5 class="m-0 text-dark">Unggah Tugas</h5>
@stop

@push('css')
@endpush

@section('content')
    <x-adminlte-card>
        @php
            \Carbon\Carbon::setLocale('id');
        @endphp

        <!-- Header Section -->
        <div class="card-header bg-gradient-warning text-dark">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-upload mr-2"></i>
                        Upload Tugas Tindak Lanjut
                    </h4>
                </div>
                <div class="col-auto">
                    <span class="badge badge-light px-3 py-2">
                        <i class="fas fa-clock mr-1"></i>
                        Submit Task
                    </span>
                </div>
            </div>
        </div>

        <!-- Body Section -->
        <div class="card-body px-4 py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">

                    <!-- Task Information Section -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light border-bottom">
                            <h6 class="mb-0 font-weight-bold text-dark">
                                <i class="fas fa-info-circle mr-2"></i>
                                Detail Penugasan
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Task Description -->
                                <div class="col-lg-6 col-md-12 mb-4 mb-lg-0">
                                    <div class="h-100 border-right border-light pr-lg-4">
                                        <div class="d-flex align-items-start mb-3">
                                            <i class="fas fa-tasks text-primary fa-lg mr-3 mt-1"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="font-weight-bold text-dark mb-2">Deskripsi Tugas</h6>
                                                <div class="bg-light rounded p-3">
                                                    <p class="mb-0 text-dark">{{ $rapatTindakLanjut->deskripsi_tugas }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Deadline -->
                                <div class="col-lg-6 col-md-12">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-calendar-alt text-warning fa-lg mr-3 mt-1"></i>
                                        <div class="flex-grow-1">
                                            <h6 class="font-weight-bold text-dark mb-2">Target Penyelesaian</h6>
                                            <div class=" bg-opacity-10 border border-warning rounded p-3">
                                                <p class="mb-0 font-weight-bold text-dark">
                                                    {{ \Carbon\Carbon::parse($rapatTindakLanjut->batas_waktu)->translatedFormat('l, d F Y') }}
                                                </p>
                                                @php
                                                    $deadline = \Carbon\Carbon::parse($rapatTindakLanjut->batas_waktu);
                                                    $now = \Carbon\Carbon::now();
                                                    $daysLeft = $now->diffInDays($deadline, false);
                                                @endphp
                                                <small class="text-muted">
                                                    @if ($daysLeft > 0)
                                                        <i class="fas fa-hourglass-half mr-1"></i>
                                                        {{ $daysLeft }} hari tersisa
                                                    @elseif($daysLeft == 0)
                                                        <i class="fas fa-exclamation-triangle text-warning mr-1"></i>
                                                        Deadline hari ini!
                                                    @else
                                                        <i class="fas fa-exclamation-circle text-danger mr-1"></i>
                                                        Terlambat {{ abs($daysLeft) }} hari
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Form Section -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 font-weight-bold text-dark">
                                <i class="fas fa-file-upload mr-2"></i>
                                Form Upload Tugas
                            </h6>
                        </div>

                        <div class="card-body">
                            <form
                                action="{{ url('/rapat/tindak-lanjut-rapat/tugas/' . $rapatTindakLanjut->slug . '/unggah-tugas') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    <!-- Task Link/URL -->
                                    <div class="col-lg-6 col-md-12 mb-4">
                                        <div class="form-group">
                                            <label for="tugas-input" class="form-label font-weight-bold text-dark">
                                                <i class="fas fa-link mr-2 text-info"></i>
                                                Link Tugas <span class="badge badge-secondary badge-pill">Opsional</span>
                                            </label>
                                            <input type="text" name="tugas"
                                                class="form-control form-control-lg @error('tugas') is-invalid @enderror"
                                                id="tugas-input" placeholder="https://contoh.com/tugas-saya">
                                            @error('tugas')
                                                <div class="invalid-feedback d-flex align-items-center">
                                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                Masukkan link jika tugas berupa dokumen online
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Obstacles/Issues -->
                                    <div class="col-lg-6 col-md-12 mb-4">
                                        <div class="form-group">
                                            <label for="kendala-input" class="form-label font-weight-bold text-dark">
                                                <i class="fas fa-exclamation-triangle mr-2 text-warning"></i>
                                                Kendala <span class="badge badge-secondary badge-pill">Opsional</span>
                                            </label>
                                            <textarea class="form-control form-control-lg @error('kendala') is-invalid @enderror" name="kendala" id="kendala-input"
                                                rows="4" placeholder="Jelaskan kendala yang dihadapi (jika ada)..."></textarea>
                                            @error('kendala')
                                                <div class="invalid-feedback d-flex align-items-center">
                                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="fas fa-question-circle mr-1"></i>
                                                Ceritakan kendala yang dialami saat mengerjakan tugas
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- File Upload Section -->
                                <div class="form-group mb-4">
                                    <label for="file-input" class="form-label font-weight-bold text-dark">
                                        <i class="fas fa-paperclip mr-2 text-success"></i>
                                        File Tugas <span class="badge badge-secondary badge-pill">Opsional</span>
                                    </label>
                                    <div class="custom-file-container border-2 border-dashed border-light rounded p-4">
                                        <input class="form-control @error('file_tugas') is-invalid @enderror"
                                            name="file_tugas[]" multiple type="file" id="file-input"
                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    </div>
                                    @foreach ($errors->get('file_tugas.*') as $fileErrors)
                                        @foreach ($fileErrors as $error)
                                            <div class="invalid-feedback d-block d-flex align-items-center">
                                                <i class="fas fa-exclamation-circle mr-2"></i>
                                                {{ $error }}
                                            </div>
                                        @endforeach
                                    @endforeach
                                    <small class="form-text text-muted">
                                        <i class="fas fa-upload mr-1"></i>
                                        Anda dapat memilih beberapa file sekaligus
                                    </small>
                                </div>

                                <!-- Action Buttons -->
                                <hr class="my-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Kembali
                                    </a>
                                    <div>
                                        <button type="submit" class="btn btn-success shadow">
                                            <i class="fas fa-paper-plane mr-2"></i>
                                            Upload Tugas
                                        </button>
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
