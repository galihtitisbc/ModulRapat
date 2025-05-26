<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Start your development with a Dashboard for Bootstrap 4.">
    <meta name="author" content="Creative Tim">
    <title>Konfirmasi Kesediaan Rapat</title>
    <!-- Favicon -->
    <link rel="icon" href="{{ url('favicon.png') }}" type="image/png">
    <!-- Icons -->
    <link rel="stylesheet" href="{{ url('argon') }}/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css"
        type="text/css">
    <!-- Argon CSS -->
    <link rel="stylesheet" href="{{ url('argon') }}/assets/css/argon.css?v=1.2.0" type="text/css">
    <script src="{{ url('js/util.js') }}"></script>
    <!-- Custom CSS -->
    <link href="{{ asset('/assets/css/halamanAwal.css') }}" rel="stylesheet">

    <style>
        .section-label {
            font-weight: 600;
            font-size: 1.1rem;
            color: #343a40;
        }

        .section-content {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: #495057;
        }
        .success-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 40px 20px;
            text-align: center;
        }
        .success-icon {
            background-color: #00a841;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(66, 133, 244, 0.3);
            animation: pulse 2s infinite;
        }
        .success-icon i {
            color: white;
            font-size: 50px;
        }
        .success-title {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #212529;
        }
        .success-message {
            font-size: 18px;
            color: #6c757d;
            margin-bottom: 30px;
            line-height: 1.5;
        }
        .btn-return {
            background-color: #4285F4;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .btn-return:hover {
            background-color: #00ff2a;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 40px 20px;
            background-color: white;
        }
    </style>
</head>

<body class="bg-default">
    @php
        use Carbon\Carbon;
        Carbon::setLocale('id');
        $waktuSelesai = $rapat->waktu_selesai == null ? 'SELESAI' : Carbon::parse($rapat->waktu_selesai)->format('H:i');
        $waktuMulai =
            Carbon::parse($rapat->waktu_mulai)->translatedFormat('l, d F Y') .
            ' | Pukul ' .
            Carbon::parse($rapat->waktu_mulai)->format('H:i');
    @endphp
    <!-- Main content -->
    <div class="main-content halaman_awal login_page">
        <!-- Header -->
        <div class="header bg-gradient-primary py-7">
            <div class="container">
                <div class="header-body text-center mb-5">
                    <div class="row justify-content-center">
                        <div class="col-lg-9 col-lg-6 col-md-8 px-2">
                        </div>
                    </div>
                </div>
            </div>
            <div class="separator separator-bottom separator-skew zindex-100">
                <svg x="0" y="0" viewBox="0 0 2560 100" preserveAspectRatio="none" version="1.1"
                    xmlns="http://www.w3.org/2000/svg">
                    <polygon class="fill-default" points="2560 0 2560 100 0 100"></polygon>
                </svg>
            </div>
        </div>
        <!-- Page content -->
        <div class="container mt--8 pb-7">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-7">
                    <div class="card">
                        <div class="card-body">
                            @if ($statusKonfirmasi !== 'MENUNGGU')
                                <div class="success-container">
                                    <div class="success-icon">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <h3>Anda Telah Mengisi Form Konfirmasi Kesediaan Mengikuti Rapat</h3>
                                </div>
                            @endif
                         @if ($statusKonfirmasi == 'MENUNGGU')
                         <form
                         action="{{ url('/rapat/agenda-rapat/konfirmasi/' . $rapat->slug . '/' . $pegawai->username) }}"
                         method="POST">
                         @csrf
                         @method('POST')

                         <!-- Detail Rapat (Hanya Teks) -->
                         <div>
                             <label class="section-label">Agenda Rapat:</label>
                             <div class="section-content">{{ $rapat->agenda_rapat }}</div>

                             <label class="section-label">Waktu:</label>
                             <div class="section-content">
                                 {{ $waktuMulai }} - {{ $waktuSelesai }}
                             </div>

                             <label class="section-label">Tempat:</label>
                             <div class="section-content">{{ $rapat->tempat }}</div>
                         </div>

                         <!-- Select Input Kesediaan -->
                         <div class="form-group mt-4">
                             <label for="kesediaan">Kesediaan Mengikuti Rapat</label>
                             <select class="form-control" id="kesediaan" name="status" required>
                                 <option value="">-- Pilih Kesediaan --</option>
                                 <option value="BERSEDIA">Bersedia</option>
                                 <option value="TIDAK_BERSEDIA">Tidak Bersedia</option>
                             </select>
                         </div>
                         <div class="mx-auto text-center mt-4">
                             <button type="submit" class="btn btn-primary">Simpan</button>
                         </div>
                     </form> @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Argon Scripts -->
    <!-- Core -->
    <script src="{{ url('argon') }}/assets/vendor/jquery/dist/jquery.min.js"></script>
    <script src="{{ url('argon') }}/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
