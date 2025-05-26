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
        }

        .meeting-title {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 20px;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
            box-shadow: var(--box-shadow);
        }

        .meeting-title h1 {
            font-size: 20px;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .meeting-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 15px 20px;
            background-color: var(--light-gray);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .meeting-info h2 {
            font-size: 16px;
            margin: 0;
            color: var(--primary-color);
        }

        .meeting-info .meeting-date {
            display: flex;
            align-items: center;
        }

        .meeting-info .meeting-date i {
            margin-right: 8px;
            color: var(--primary-color);
        }

        /* Section styling */
        .section {
            margin-bottom: 30px;
            background-color: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--box-shadow);
            border-left: 4px solid var(--primary-color);
        }

        .section-title {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: var(--primary-color);
            border-bottom: 2px solid var(--secondary-color);
            padding-bottom: 8px;
        }

        .section-title i {
            margin-right: 10px;
            font-size: 18px;
        }

        .section-title h3 {
            font-size: 16px;
            margin: 0;
            font-weight: 600;
        }

        .attendee-card {
            background-color: var(--light-gray);
            padding: 10px;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
        }

        .attendee-card i {
            color: var(--primary-color);
            margin-right: 8px;
            font-size: 16px;
        }

        /* Agenda styling */
        .agenda-content {
            background-color: var(--light-gray);
            padding: 15px;
            border-radius: var(--border-radius);
            margin-top: 10px;
        }

        /* Attachments */
        .attachment-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .attachment-item {
            display: flex;
            align-items: center;
            background-color: var(--light-gray);
            padding: 8px 12px;
            border-radius: var(--border-radius);
            width: fit-content;
            transition: all 0.3s ease;
        }

        .attachment-item:hover {
            background-color: #e0e0e0;
        }

        .attachment-item i {
            color: var(--primary-color);
            margin-right: 8px;
        }

        .attachment-item a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            margin-left: 8px;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th {
            background-color: var(--primary-color);
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 500;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }

        table tr:nth-child(even) {
            background-color: var(--light-gray);
        }

        table tr:hover {
            background-color: #e8f4fd;
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
            color: red;
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
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .gallery-item {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: transform 0.3s ease;
        }

        .gallery-item:hover {
            transform: scale(1.02);
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
            font-size: 12px;
            color: var(--dark-gray);
            padding: 20px 0;
            border-top: 1px solid #e0e0e0;
        }

        /* Notes */
        .notes-content {
            background-color: var(--light-gray);
            padding: 15px;
            border-radius: var(--border-radius);
            margin-top: 10px;
            line-height: 1.7;
        }

        /* Icons */
        .material-icons {
            font-family: 'Material Icons';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            display: inline-block;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
            direction: ltr;
        }

        .btn-print {
            margin-top: 30px;
            display: inline-block;
            padding: 5px 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 9pt;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        @media print {
            .web-only {
                display: none !important;
            }

            .meeting-title {
                background-color: var(--primary-color) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
@endpush
@section('content')
    @php
        use Carbon\Carbon;
        use Modules\Rapat\Http\Helper\StatusTindakLanjut;

        Carbon::setLocale('id');
        $waktuMulai = Carbon::parse($rapat['waktu_mulai'])->translatedFormat('l, d F Y H:i');
    @endphp

    <div class="meeting-title">
        <h1>NOTULENSI RAPAT</h1>
    </div>

    <div class="meeting-info">
        <div class="meeting-date">
            <i class="fas fa-calendar-alt"></i>
            <h2>{{ $rapat->waktu_mulai }}</h2>
        </div>
    </div>

    <div class="section">
        <div class="section-title">
            <i class="fas fa-calendar-check"></i>
            <h3>Agenda Rapat</h3>
        </div>
        <div class="agenda-content">
            <p>{{ $rapat->agenda_rapat }}</p>
        </div>
    </div>
    <div class="section">
        <div class="section-title">
            <i class="fas fa-users"></i>
            <h3>Daftar Hadir</h3>
        </div>
        <div class="attendees-grid">
            <table>
                <thead>
                    <th>No</th>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Status</th>
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
                            <td>{{ $item->formatted_name }}</td>
                            <td>
                                <span class="status-badge {{ $statusHadirClass }}">
                                    {{ $item->pivot->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

    <div class="section">
        <div class="section-title">
            <i class="fas fa-user-edit"></i>
            <h3>Notulis</h3>
        </div>
        <div class="attendee-card">
            <i class="fas fa-pen"></i>
            {{ $rapat->rapatAgendaNotulis->formatted_name }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">
            <i class="fas fa-paperclip"></i>
            <h3>Lampiran Rapat</h3>
        </div>
        <div class="attachment-list">
            @foreach ($rapat->rapatLampiran as $item)
                <div class="attachment-item">
                    <i class="fas fa-file-alt"></i>
                    <a href="{{ url('/rapat/agenda-rapat/' . $item->nama_file . '/download') }}">
                        {{ $item->nama_file }}
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <div class="section">
        <div class="section-title">
            <i class="fas fa-file-alt"></i>
            <h3>Notulen Rapat</h3>
        </div>
        @if ($rapat->rapatNotulen->notulenFiles->isNotEmpty())
            <div class="attachment-list">
                @foreach ($rapat->rapatNotulen->notulenFiles as $item)
                    <div class="attachment-item">
                        <i class="fas fa-file-word"></i>
                        <a href="{{ url('/rapat/agenda-rapat/notulis/' . $item->nama_file . '/download') }}">
                            {{ $item->nama_file }}
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
        <div class="section-title">
            <i class="fas fa-tasks"></i>
            <h3>Penugasan Tindak Lanjut Rapat</h3>
        </div>
        @if ($rapat->rapatTindakLanjut->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 25%">Nama</th>
                        <th style="width: 50%">Tugas</th>
                        <th style="width: 20%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rapat->rapatTindakLanjut as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->pegawai->formatted_name }}
                            </td>
                            <td>{{ $item->deskripsi_tugas }}</td>
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
        <div class="section-title">
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
@endsection
