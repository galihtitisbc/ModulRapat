<script>
    //untuk handle jika rapat adalah rapat kepanitiaan, maka auto check pada peserta rapat sesuai
    //anggota kepanitiaan
    let lastPesertaKepanitiaanChange = [];
    // untuk menampung data id kepanitiaan yang telah dipilih sebelumnya
    let lastKepanitiaanId = null;
    $("#kepanitiaan").on("change", function() {
        let kepanitiaan_id = $(this).val();
        $.ajax({
            url: `/rapat/agenda-rapat/ajax-kepanitiaan/${
            kepanitiaan_id ? kepanitiaan_id : "-"
        }`,
            type: "GET",
            dataSrc: "data",
            success: function(response) {
                // jika kepanitiaan diganti, maka akan menghapus semua peserta kepanitiaan yang lama
                // isUpdate && isFirstLoad digunakan saat update, jadi akan memperbarui data peserta kepanitiaan
                // dan menhapus data peserta kepanitiaan yang telah dipilih sebelumnya
                // saya buat begini agar data antar kepanitiaan tidak menumpuk
                if (lastKepanitiaanId !== null || isUpdate && isFirstLoad) {
                    let $selectPesertaChange = $(".duallistbox-peserta-rapat");
                    let currentSelectedChange = ($selectPesertaChange.val() || []).map(Number);
                    let filteredSelectedChange;
                    if (lastKepanitiaanId !== response.id) {
                        // menghapus semua data peserta kepanitiaan yang telah di pilih sebelum nya
                        filteredSelectedChange = currentSelectedChange.filter((val) => !
                            lastPesertaKepanitiaanChange
                            .includes(val));
                        $selectPesertaChange.val(filteredSelectedChange);
                        $selectPesertaChange.trigger('change');
                    }
                    // jika isUpdate === true dan pertama kali load
                    if (isUpdate && isFirstLoad) {
                        isFirstLoad = false;
                        filteredSelectedChange = currentSelectedChange.filter((val) => !
                            lastKepanitiaanSelectedIdUpdate
                            .includes(val));
                    }
                    $selectPesertaChange.val(filteredSelectedChange);
                    $selectPesertaChange.trigger('change');
                }
                pesertaKepanitiaan = response.pegawai.map(
                    (pegawai) => pegawai.id
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
                lastKepanitiaanId = response.id;
                lastPesertaKepanitiaanChange = [...pesertaKepanitiaan];
            },
            error: function(xhr) {
                // menghapus semua data peserta kepanitiaan yang telah di pilih sebelum nya
                let $selectPeserta = $(".duallistbox-peserta-rapat");
                let currentSelected = ($selectPeserta.val() || []).map(Number);
                let filteredSelected;
                // jika isUpdate === true dan pertama kali load
                if (isUpdate && isFirstLoad) {
                    isFirstLoad = false;
                    filteredSelected = currentSelected.filter((val) => !
                        lastKepanitiaanSelectedIdUpdate
                        .includes(val));
                } else {
                    filteredSelected = currentSelected.filter((val) => !lastPesertaKepanitiaan
                        .includes(val));
                }
                $selectPeserta.val(filteredSelected);
                $selectPeserta.trigger('change');

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
