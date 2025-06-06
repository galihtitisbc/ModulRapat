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

    <div class="signature-section">
        <div class="signature-box">
            {{-- <div class="tanggal-tempat">Banyuwangi, {{ $tglTtd }}</div>
            <div>Direktur,</div> --}}
            {{-- <div class="qr-code">
                <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="60" height="60" fill="white" />
                    <rect x="5" y="5" width="50" height="50" fill="none" stroke="black" stroke-width="1" />
                    <rect x="8" y="8" width="8" height="8" fill="black" />
                    <rect x="20" y="8" width="4" height="4" fill="black" />
                    <rect x="28" y="8" width="4" height="4" fill="black" />
                    <rect x="36" y="8" width="4" height="4" fill="black" />
                    <rect x="44" y="8" width="8" height="8" fill="black" />
                    <rect x="8" y="16" width="4" height="4" fill="black" />
                    <rect x="16" y="16" width="4" height="4" fill="black" />
                    <rect x="24" y="16" width="8" height="8" fill="black" />
                    <rect x="36" y="16" width="4" height="4" fill="black" />
                    <rect x="44" y="16" width="4" height="4" fill="black" />
                    <rect x="8" y="24" width="4" height="4" fill="black" />
                    <rect x="16" y="24" width="8" height="8" fill="black" />
                    <rect x="28" y="24" width="4" height="4" fill="black" />
                    <rect x="36" y="24" width="8" height="8" fill="black" />
                    <rect x="48" y="24" width="4" height="4" fill="black" />
                    <rect x="8" y="32" width="8" height="8" fill="black" />
                    <rect x="20" y="32" width="4" height="4" fill="black" />
                    <rect x="28" y="32" width="8" height="8" fill="black" />
                    <rect x="40" y="32" width="4" height="4" fill="black" />
                    <rect x="48" y="32" width="4" height="4" fill="black" />
                    <rect x="8" y="44" width="8" height="8" fill="black" />
                    <rect x="20" y="44" width="4" height="4" fill="black" />
                    <rect x="28" y="44" width="4" height="4" fill="black" />
                    <rect x="36" y="44" width="8" height="8" fill="black" />
                    <rect x="48" y="44" width="4" height="4" fill="black" />
                </svg>
            </div>
            <div class="jabatan">Prof. Dr. Ir. Supriadi, M.T.<br>NIP. 196512201990031002</div> --}}
        </div>
    </div>
@endsection
