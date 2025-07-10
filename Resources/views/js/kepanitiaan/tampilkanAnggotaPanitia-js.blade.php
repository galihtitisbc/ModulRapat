<script>
    $('.select-kepanitiaan').select2();
    //untuk menampilkan data anggota panitia yang dipilih
    let tableAnggotaPanitia = $("#table-anggota-panitia").DataTable({
        serverSide: true,
        processing: true,
        autoWidth: false,
        ajax: {
            url: `{{ route('rapat.agenda.ajax.selected.peserta') }}`,
            type: "GET",
            data: function(d) {
                d.id = pesertaKepanitiaan.join(",");
            },
            dataSrc: "data",
        },
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

        ],
        pageLength: 5,
        lengthChange: true,
        searching: true,
        ordering: true,
    });
</script>
