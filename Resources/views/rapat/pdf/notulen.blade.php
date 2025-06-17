@extends('rapat::rapat.pdf.pdf_layout')
@push('css')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #0067b3;
            --secondary-color: #ffc107;
            --text-color: #333;
            --light-gray: #f5f5f5;
            --dark-gray: #666;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --border-color: #333;
            --medium-gray: #e9ecef;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: white;
            padding: 40px;
            max-width: 210mm;
            margin: 0 auto;
        }

        .meeting-title {
            text-align: center;
            border: 2px solid var(--border-color);
            padding: 20px;
            margin-bottom: 30px;
        }

        .meeting-title h1 {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .meeting-info {
            margin-bottom: 25px;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .meeting-info h2 {
            font-size: 14px;
            font-weight: normal;
        }

        .meeting-info .meeting-date {
            display: flex;
            align-items: center;
        }

        .meeting-info .meeting-date i {
            margin-right: 8px;
        }

        /* Section styling */
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .section-title h3 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section-title i {
            margin-right: 8px;
            font-size: 14px;
        }

        .attendee-card {
            padding: 10px 0;
            border-bottom: 1px dotted var(--border-color);
        }

        .attendee-card i {
            margin-right: 8px;
        }

        /* Agenda styling */
        .agenda-content {
            border: 1px solid var(--border-color);
            padding: 15px;
            margin-top: 10px;
            text-align: justify;
        }

        /* Attachments */
        .attachment-list {
            margin-top: 10px;
        }

        .attachment-item {
            display: flex;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dotted var(--medium-gray);
        }

        .attachment-item i {
            margin-right: 8px;
            width: 20px;
        }

        .attachment-item a {
            color: var(--text-color);
            text-decoration: underline;
            margin-left: 8px;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            border: 2px solid var(--border-color);
        }

        table th {
            background-color: var(--light-gray);
            padding: 12px 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid var(--border-color);
            font-size: 12px;
            text-transform: uppercase;
        }

        table td {
            padding: 10px 8px;
            border: 1px solid var(--border-color);
            text-align: center;
            font-size: 12px;
        }

        table tr:nth-child(even) {
            background-color: var(--light-gray);
        }

        /* Status badges */
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .status-pending {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-complete {
            background-color: #d4edda;
            color: #155724;
        }

        .status-progress {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        /* Documentation */
        .documentation-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .gallery-item {
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .gallery-item img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Footer styling */
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            padding: 20px 0;
            border-top: 2px solid var(--border-color);
        }

        /* Notes */
        .notes-content {
            border: 1px solid var(--border-color);
            padding: 15px;
            margin-top: 10px;
            line-height: 1.8;
            text-align: justify;
            margin: 5px;
        }

        .empty-state {
            text-align: center;
            padding: 20px;
            font-style: italic;
            border: 1px dashed var(--border-color);
        }

        .btn-print {
            margin-top: 30px;
            padding: 8px 16px;
            background-color: white;
            color: var(--text-color);
            border: 2px solid var(--border-color);
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .btn-print:hover {
            background-color: var(--light-gray);
        }

        /* Print styles */
        @media print {
            body {
                padding: 20px;
                font-size: 12px;
            }

            .web-only {
                display: none !important;
            }

            .section {
                page-break-inside: avoid;
            }

            .meeting-title {
                page-break-after: avoid;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 20px 15px;
            }

            .documentation-gallery {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 10px;
            }

            table th,
            table td {
                padding: 6px 4px;
            }
        }
    </style>
@endpush
@section('content')
    @php
        use Carbon\Carbon;
        use Modules\Rapat\Http\Helper\StatusTindakLanjut;
        use Modules\Rapat\Http\Helper\StatusPesertaRapat;

        Carbon::setLocale('id');
        $waktuMulai = Carbon::parse($rapat['waktu_mulai'])->translatedFormat('l, d F Y H:i');
    @endphp
    <div class="meeting-title">
        <h1>NOTULENSI RAPAT</h1>
    </div>

    <div class="meeting-info">
        <div class="meeting-date">
            <i class="fas fa-calendar-alt"></i>
            <h2>{{ $waktuMulai }}</h2>
        </div>
    </div>

    <div class="section">
        <div class="section-title" style="display: flex; align-items: center">
            <i class="fas fa-calendar-check"></i>
            <h3>Agenda Rapat</h3>
        </div>
        <div class="agenda-content">
            <p>{{ $rapat->agenda_rapat }}</p>
        </div>
    </div>

    <div class="section">
        <div class="section-title" style="display: flex; align-items: center">
            <i class="fas fa-users"></i>
            <h3>Daftar Hadir</h3>
        </div>
        <div class="attendees-grid">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rapat->rapatAgendaPeserta as $item)
                        @php
                            $statusHadirClass = '';
                            if ($item->pivot->status == 'TIDAK_HADIR') {
                                $statusHadirClass = 'status-pending';
                            } else {
                                $statusHadirClass = 'status-complete';
                            }
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nip }}</td>
                            <td style="text-align: left">{{ $item->formatted_name }}</td>
                            <td>
                                <span class="status-badge {{ $statusHadirClass }}">
                                    {{ StatusPesertaRapat::from($item->pivot->status)->label() }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title" style="display: flex; align-items: center">
            <i class="fas fa-user-edit"></i>
            <h3>Notulis</h3>
        </div>
        <div class="attendee-card">
            <i class="fas fa-pen"></i>
            {{ $rapat->rapatAgendaNotulis->formatted_name }}
        </div>
    </div>

    <div class="section">
        <div class="section-title" style="display: flex; align-items: center">
            <i class="fas fa-paperclip"></i>
            <h3>Lampiran Rapat</h3>
        </div>
        <div class="attachment-list">
            @foreach ($rapat->rapatLampiran as $item)
                <div class="attachment-item">
                    <i class="fas fa-file-alt"></i>
                    <span>{{ $item->nama_file }}</span>
                    <a href="{{ url('/rapat/agenda-rapat/' . $item->nama_file . '/download') }}">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <div class="section">
        <div class="section-title" style="display: flex; align-items: center">
            <i class="fas fa-file-alt"></i>
            <h3>Notulen Rapat</h3>
        </div>
        @if ($rapat->rapatNotulen->notulenFiles->isNotEmpty())
            <div class="attachment-list">
                @foreach ($rapat->rapatNotulen->notulenFiles as $item)
                    <div class="attachment-item">
                        <i class="fas fa-file-word"></i>
                        <span>{{ $item->nama_file }}</span>
                        <a href="{{ url('/rapat/notulis/' . $item->nama_file . '/download') }}">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($rapat->rapatNotulen->catatan != null)
            <div class="notes-content">
                {!! strip_tags($rapat->rapatNotulen->catatan, '<b><strong><i><u><p><div><span><ul><ol><li><br>') !!}
            </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title" style="display: flex; align-items: center">
            <i class="fas fa-tasks"></i>
            <h3>Penugasan Tindak Lanjut Rapat</h3>
        </div>
        @if ($rapat->rapatTindakLanjut->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Tugas</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rapat->rapatTindakLanjut as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td style="text-align: left">{{ $item->pegawai->formatted_name }}</td>
                            <td style="text-align: left">{{ $item->deskripsi_tugas }}</td>
                            <td>
                                @php
                                    $status = StatusTindakLanjut::from($item->status)->value;
                                    $statusClass = '';
                                    if ($status == StatusTindakLanjut::BELUM_SELESAI->value) {
                                        $statusClass = 'status-pending';
                                    } else {
                                        $statusClass = 'status-complete';
                                    }
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    {{ StatusTindakLanjut::from($item->status)->label() }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <p><i class="fas fa-info-circle"></i> Tidak Ada Tugas</p>
            </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title" style="display: flex; align-items: center">
            <i class="fas fa-images"></i>
            <h3>Dokumentasi Rapat</h3>
        </div>
        <div class="documentation-gallery">
            @foreach ($rapat->rapatDokumentasi as $dok)
                <div class="gallery-item">
                    <img src="{{ asset('/storage/dokumentasi-rapat/' . $dok->foto . '') }}" alt="Dokumentasi Rapat">
                </div>
            @endforeach
        </div>
    </div>

    <script>
        // Set current date
        document.getElementById('current-date').textContent = new Date().toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    </script>
@endsection
