<script>
    //untuk handle jika rapat adalah rapat kepanitiaan, maka auto check pada peserta rapat sesuai
    //anggota kepanitiaan
    $("#kepanitiaan").on("change", function() {
        let kepanitiaan_id = $(this).val();
        $.ajax({
            url: `/rapat/agenda-rapat/ajax-kepanitiaan/${
            kepanitiaan_id ? kepanitiaan_id : "-"
        }`,
            type: "GET",
            dataSrc: "data",
            success: function(response) {
                pesertaKepanitiaan = response.pegawai.map(
                    (pegawai) => pegawai.username
                );
                lastPesertaKepanitiaan = [...pesertaKepanitiaan];
                /// untuk rapat kepanitiaan
                tableAnggotaPanitia.ajax.reload();

                // ✅ Select pesertaKepanitiaan di duallistPesertaRapat
                let $selectPeserta = $(".duallistbox-peserta-rapat");
                $selectPeserta.val([...new Set([...$selectPeserta.val() || [], ...
                    pesertaKepanitiaan
                ])]);
                // ✅ Trigger change agar updateOpsiBerdasarkanPeserta dijalankan
                $selectPeserta.trigger('change');
                // ✅ Refresh semua duallistbox
                $selectPeserta.bootstrapDualListbox('refresh');
                $(".duallistbox-pimpinan-rapat").bootstrapDualListbox('refresh');
                $(".duallistbox-notulis-rapat").bootstrapDualListbox('refresh');


                $('.table-anggota-panitia-group').removeClass('d-none').show();
                $('#peserta-rapat-label').text('Pilih Pegawai Diluar Kepanitiaan ( Jika Ada ) :');
                //digunakan untuk menyembuyinkan table anggota panitia yang telah dipilih pada form edit
                $('#table-anggota-panitia-before').addClass('d-none').hide();
            },
            error: function(xhr) {
                let $selectPeserta = $(".duallistbox-peserta-rapat");
                let currentSelected = $selectPeserta.val() || [];
                let filteredSelected = currentSelected.filter((val) => !lastPesertaKepanitiaan
                    .includes(val));
                $selectPeserta.val(filteredSelected);
                $selectPeserta.trigger('change');
                console.log('Data Telah terhapus');

                $selectPeserta.bootstrapDualListbox('refresh');
                $(".duallistbox-pimpinan-rapat").bootstrapDualListbox('refresh');
                $(".duallistbox-notulis-rapat").bootstrapDualListbox('refresh');

                tableAnggotaPanitia.ajax.reload();
                $('.table-anggota-panitia-group').addClass('d-none').hide();
                $('#peserta-rapat-label').text('Pilih Peserta Rapat :');
            },
        });
    });
</script>
