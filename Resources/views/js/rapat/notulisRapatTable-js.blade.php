<script>
    //untuk table menampilkan notulis rapat
    let tableNotulisRapat = $("#table-notulis-rapat").DataTable({
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
                    if (row.username == pimpinanRapatUsername) {
                        return "-";
                    }
                    return `<input type="radio" name="notulis_username" class="select-radio select-notulis" data-id="${
                    row.username
                }" ${notulisRapatUsername === row.username ? "checked" : ""}>`;
                },
            },
        ],
        pageLength: 10,
        lengthChange: true,
        searching: true,
        ordering: true,
    });
    $("#table-notulis-rapat").on("click", ".select-notulis", function(event) {
        notulisRapatUsername = $(this).data("id");
        tablePimpinanRapat.draw();
    });
</script>
