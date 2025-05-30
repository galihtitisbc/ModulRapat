<script>
    //untuk table menampilkan pimpinan rapat
    let tablePimpinanRapat = $("#table-pimpinan-rapat").DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: `/rapat/agenda-rapat/ajax-selected-peserta`,
            type: "GET",
            data: function(d) {
                d.username = pesertaRapat.join(",");
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
            {
                data: "user.email",
                render: function(data, type, row) {
                    return data ? data : "-";
                },
            },
            {
                data: null,
                render: function(data, type, row) {
                    if (row.username == notulisRapatUsername) {
                        return "-";
                    }
                    return `<input type="radio" name="pimpinan_username" class="select-radio select-pimpinan" data-id="${
                    row.username
                }" ${pimpinanRapatUsername === row.username ? "checked" : ""}>`;
                },
            },
        ],
        pageLength: 10,
        lengthChange: true,
        searching: true,
        ordering: true,
    });
    $("#table-pimpinan-rapat").on("click", ".select-pimpinan", function(event) {
        pimpinanRapatUsername = $(this).data("id");
        tableNotulisRapat.draw();
    });
</script>
