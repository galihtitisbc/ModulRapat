<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Tugas Kepanitiaan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css"
        integrity="sha512-rqQltXRuHxtPWhktpAZxLHUVJ3Eombn3hvk9PHjV/N5DMUYnzKPC1i3ub0mEXgFzsaZNeJcoE0YHq0j/GFsdGg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            background: white;
        }

        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }

        .kop-surat {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #000;
        }

        .kop-surat img {
            width: 120px;
            height: 120px;
            margin-right: 20px;
            object-fit: contain;
        }

        .kop-surat-text {
            flex: 1;
            text-align: center;
        }

        .kop-surat-text strong {
            font-weight: bold;
            font-size: 14pt;
        }

        .nomor-surat {
            text-align: center;
            margin: 30px 0;
            font-size: 14pt;
        }

        .judul-surat {
            text-align: center;
            font-weight: bold;
            font-size: 16pt;
            margin-bottom: 10px;
            text-decoration: underline;
        }

        .nomor-sk {
            text-align: center;
            margin-bottom: 30px;
            font-size: 12pt;
        }

        .content {
            margin: 30px 0;
            text-align: justify;
            line-height: 1.8;
        }

        .content p {
            margin-bottom: 15px;
        }

        .kepanitiaan-info {
            margin: 25px 0;
        }

        .kepanitiaan-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .kepanitiaan-info td {
            padding: 8px 5px;
            vertical-align: top;
        }

        .kepanitiaan-info td:first-child {
            width: 200px;
            font-weight: bold;
        }

        .anggota-list {
            margin: 20px 0;
        }

        .anggota-list table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }

        .anggota-list th,
        .anggota-list td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
        }

        .anggota-list th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }

        .signature-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 50px;
            margin-bottom: 20px;
        }

        .signature-box {
            text-align: center;
            width: 250px;
        }

        .tanggal-tempat {
            margin-bottom: 40px;
        }

        .qr-code {
            width: 80px;
            height: 80px;
            border: 1px solid #ccc;
            display: inline-block;
            margin: 10px 0;
            background-color: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #666;
        }

        .jabatan {
            font-weight: bold;
            margin-top: 10px;
        }

        .penutup {
            margin: 30px 0;
            text-align: justify;
        }

        .web-only {
            text-align: center;
            margin-bottom: 20px;
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

        /* Styling untuk Mengetahui */
        .signature-footer {
            text-align: center;
            margin-top: 15px;
            page-break-inside: avoid;
            padding-bottom: 20px;
        }

        .signature-name {
            margin-top: 40px;
            font-weight: bold;
        }

        .digital-stamp {
            display: flex;
            align-items: center;
            border: 1px solid #000;
            padding: 2px 4px;
            max-width: 260px;
            font-size: 7pt;
            line-height: 1.1;
            margin: 5px auto 0 auto;
            background-color: white;
        }

        .stamp-logo {
            flex-shrink: 0;
            margin-right: 6px;
        }

        .stamp-logo img {
            width: 28px;
            height: auto;
        }

        .stamp-text {
            flex-grow: 1;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .web-only {
                display: none !important;
            }

            .container {
                max-width: none;
                padding: 0;
            }
        }
    </style>
    @stack('css')
</head>

<body>
    <div class="container">
        <!-- Tombol Print Preview -->
        <div class="web-only">
            <button class="btn-print" onclick="window.print()">🖨 Tampilkan Print Preview</button>
        </div>
        <div class="kop-surat">
            <img src="{{ asset('assets/img/pdf/Logo_Politeknik_Negeri_Banyuwangi.png') }}"
                alt="Logo Politeknik Negeri Banyuwangi">
            <div class="kop-surat-text">
                <strong>KEMENTERIAN PENDIDIKAN TINGGI, SAINS,</strong><br>
                <strong>DAN TEKNOLOGI</strong><br>
                <strong>POLITEKNIK NEGERI BANYUWANGI</strong><br>
                Jl. Raya Jember kilometer 13 Labanasem, Kabat, Banyuwangi, 68461<br>
                Telp/Faks: (0333) 636780 <br>
                E-mail: <u>poliwangi@poliwangi.ac.id</u> ; <u>Website : http//www.poliwangi.ac.id</u>
            </div>
        </div>
        @yield('content')
    </div>

    <script>
        function printPage() {
            window.print();
        }
    </script>
</body>

</html>
