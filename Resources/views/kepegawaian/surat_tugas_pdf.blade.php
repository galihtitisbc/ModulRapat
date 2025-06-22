@extends('rapat::rapat.pdf.pdf_layout')

@section('content')
    @php
        use Carbon\Carbon;
        Carbon::setLocale('id');
        $mulai = Carbon::parse($kepanitiaan->tanggal_mulai)->translatedFormat('d F Y');
        $berakhir = Carbon::parse($kepanitiaan->tanggal_berakhir)->translatedFormat('d F Y');
        $tglTtd = Carbon::parse($kepanitiaan->created_at)->translatedFormat('d F Y');
        $rentangTanggal = "{$mulai} - {$berakhir}";

        //untuk mengambil ketua dan anggota panitia
        $pegawaiKetua = $kepanitiaan->pegawai->firstWhere('username', $kepanitiaan->ketua->username);
        $pegawaiAnggota = $kepanitiaan->pegawai->reject(fn($p) => $p->username === $kepanitiaan->ketua->username);
        $pegawaiList = collect([$pegawaiKetua])->merge($pegawaiAnggota);
    @endphp
    <div class="judul-surat">
        SURAT TUGAS
    </div>

    {{-- <div class="nomor-sk">
    Nomor: 001/SK/POLIWANGI/V/2025
</div> --}}

    <div class="content">
        <p>{{ $kepanitiaan->deskripsi }}</p>
    </div>

    <div class="content">
        <p><strong>Struktur Kepanitiaan: {{ $kepanitiaan->nama_kepanitiaan }}</strong></p>
    </div>

    <div class="anggota-list">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Jabatan dalam Kepanitiaan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>{{ $kepanitiaan->ketua->nip }}</td>
                    <td>{{ $kepanitiaan->ketua->formatted_name }}</td>
                    <td>Ketua Panitia</td>
                </tr>
                @foreach ($struktur as $index => $anggota)
                    <tr>
                        <td>{{ $index + 1 + 1 }}</td>
                        <td>{{ $anggota['pegawai']->nip }}</td>
                        <td>{{ $anggota['pegawai']->formatted_name }}</td>
                        <td>{{ $anggota['jabatan'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="penutup">
        <p>Demikian surat keputusan pembentukan kepanitiaan ini dibuat untuk dapat dilaksanakan dengan penuh
            tanggung jawab. Kepanitiaan yang telah dibentuk diharapkan dapat menjalankan tugas dan fungsinya dengan
            baik demi kelancaran kegiatan yang dimaksud.</p>
    </div>

    <div class="signature-footer">
        <p style="margin: 3px 0;">Mengetahui,</p>
        <p style="margin: 3px 0;">Pejabat Pembuat Komitmen,</p>
        <div class="digital-stamp">
            <div class="stamp-logo">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo Instansi">
            </div>
            <div class="stamp-text">
                Ditandatangani secara elektronik oleh<br>
                Direktur Politeknik Negeri Banyuwangi<br>
                selaku Pejabat yang Berwenang
            </div>
            <div>
                <img src="data:image/svg+xml;base64,{{ base64_encode($qrCodeImage) }}" alt="QR Code"
                    style="width: 50px; height: 50px;" />
            </div>
        </div>
        <p class="signature-name">
            Ir. M. Shofi'ul Amin, S.T., M.T
            {{-- {{ $perjalanan->pejabat->pegawai->gelar_dpn ?? '' }}{{ $perjalanan->pejabat->pegawai->gelar_dpn ? ' ' : '' }}{{ $perjalanan->pejabat->pegawai->nama }}{{ $perjalanan->pejabat->pegawai->gelar_blk ? ', ' . $perjalanan->pejabat->pegawai->gelar_blk : '' }} --}}
        </p>
        <p style="margin: 3px 0;">
            {{-- {{ $perjalanan->pejabat->pegawai->nip }} --}}
        </p>
    </div>
    {{-- <div class="signature-section">
        <div class="signature-box">
            <div class="tanggal-tempat">Banyuwangi, {{ $tglTtd }}</div>
            <div>Direktur,</div>
            <img src="data:image/svg+xml;base64,{{ base64_encode($qrCodeImage) }}" alt="QR Code"
                style="width: 80px; height: 80px;" />
            <div class="jabatan">Prof. Dr. Ir. Supriadi, M.T.<br>NIP. 196512201990031002</div>
        </div>
    </div> --}}
@endsection
