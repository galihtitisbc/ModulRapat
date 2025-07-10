<script>
    let tableStrukturKepanitiaan = $("#table-struktur-kepanitiaan").DataTable({
        serverSide: true,
        processing: true,
        ordering: false,
        ajax: {
            url: `{{ route('rapat.agenda.ajax.selected.peserta') }}`,
            type: "GET",
            data: function(d) {
                d.id = pesertaRapat.join(",");
            },
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
                data: "kepanitiaans_aktif_bulan_ini_count",
                render: function(data, type, row) {
                    return data + 1;
                },
            },
            {
                data: null,
                render: function(data, type, row) {
                    const isPimpinan = row.id == pimpinanKepanitiaan;
                    const content = isPimpinan ?
                        "Ketua Panitia" :
                        `<input 
                            type="text" 
                            class="form-control jabatan-input" 
                            data-id="${row.id}" 
                            placeholder="Jabatan Dalam Panitia"
                        >`;
                    return `<div class="jabatan-cell" data-id="${row.id}">${content}</div>`;
                },
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `<input type="radio" name="pimpinan_id" ${
                    row.id == pimpinanKepanitiaan ? "checked" : ""
                } class="select-pimpinan text-center select-radio" id="" data-id="${
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
    $("#table-struktur-kepanitiaan").on("click", ".select-pimpinan", function() {
        const selectedId = $(this).data("id");
        pimpinanKepanitiaan = selectedId;

        // Kembalikan semua ke input (reset dari "Ketua Panitia" jika sebelumnya sudah diganti)
        $("#table-struktur-kepanitiaan .jabatan-cell").each(function() {
            const id = $(this).data("id");
            // Cek apakah elemen saat ini adalah input atau "Ketua Panitia"
            let currentValue = "";
            const input = $(this).find("input.jabatan-input");
            if (input.length) {
                currentValue = input.val();
            }

            // Render ulang input dengan value yang lama
            const inputHtml = `<input 
                        type="text" 
                        class="form-control jabatan-input" 
                        data-id="${id}" 
                        placeholder="Jabatan Dalam Panitia"
                        value="${currentValue}"
                    >`;
            $(this).html(inputHtml);
        });

        // Ganti cell yang sesuai dengan pimpinan menjadi label "Ketua Panitia"
        $(
            `#table-struktur-kepanitiaan .jabatan-cell[data-id='${selectedId}']`
        ).html("Ketua Panitia");
    });
</script>
