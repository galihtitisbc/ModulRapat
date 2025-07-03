<script>
    //untuk menampilkan daftar pegawai untuk dijadikan sebagai peserta panitia
    let tablePesertaRapat = $("#table-peserta-panitia").DataTable({
        serverSide: true,
        processing: true,
        ordering: false,
        ajax: {
            url: "/rapat/agenda-rapat/ajax-peserta-rapat",
            type: "GET",
            dataSrc: "data",
        },
        columnDefs: [{
                orderable: false,
                targets: '_all'
            } // Nonaktifkan semua kolom
        ],
        columns: [{
                data: null,
                render: function(data, type, row, meta) {
                    return meta.settings._iDisplayStart + meta.row + 1;
                },
            },
            {
                data: "nama",
                render: function(data, type, row) {
                    let gelarDepan = row.gelar_dpn ? row.gelar_dpn + " " : "";
                    let gelarBelakang = row.gelar_blk ? ", " + row.gelar_blk : "";

                    let namaFormatted = data
                        .toLowerCase()
                        .replace(/\b\w/g, function(char) {
                            return char.toUpperCase();
                        });
                    return gelarDepan + namaFormatted + gelarBelakang;
                },
            },
            {
                data: "user.email",
                render: function(data, type, row) {
                    return data ? data : "-";
                },
            },
            {
                data: "kepanitiaans_aktif_bulan_ini_count",
                render: function(data, type, row) {
                    return data;
                },
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `<input type="checkbox" ${
                    pesertaRapat.includes(row.id) ? "checked" : ""
                } class="select-checkbox add-peserta" name="peserta[]" data-id="${
                    row.id
                }">`;
                },
            },
        ],
        pageLength: 10,
        lengthChange: true,
        searching: true,
        ordering: true,
    });
    //function untuk menambahkan data peserta panitia ke dalam array
    $("#table-peserta-panitia").on("click", ".add-peserta", function(event) {
        const pegawaiId = $(this).data("id");

        if (pesertaManual.includes(pegawaiId)) {
            pesertaManual = pesertaManual.filter((item) => item !== pegawaiId);
        } else if (pesertaKepanitiaan.includes(pegawaiId)) {
            pesertaKepanitiaan = pesertaKepanitiaan.filter(
                (item) => item !== pegawaiId
            );
        } else {
            pesertaManual.push(pegawaiId);
        }
        pesertaRapat = [...new Set([...pesertaManual, ...pesertaKepanitiaan])];
        reloadTable();

    });
</script>
