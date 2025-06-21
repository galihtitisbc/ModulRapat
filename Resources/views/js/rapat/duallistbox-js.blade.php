<script>
    let duallistPesertaRapat = $('.duallistbox-peserta-rapat').bootstrapDualListbox({
        preserveSelectionOnMove: 'moved',
        moveOnSelect: false,
        selectorMinimalHeight: 400
    });

    let duallistPimpinanRapat = $('.duallistbox-pimpinan-rapat').bootstrapDualListbox({
        preserveSelectionOnMove: 'moved',
        moveOnSelect: false,
        selectorMinimalHeight: 400
    });

    let duallistNotulisRapat = $('.duallistbox-notulis-rapat').bootstrapDualListbox({
        preserveSelectionOnMove: 'moved',
        moveOnSelect: false,
        selectorMinimalHeight: 400
    });
    // Fungsi untuk menyaring opsi berdasarkan peserta yang dipilih
    function updateOpsiBerdasarkanPeserta() {
        let selectedPeserta = $('.duallistbox-peserta-rapat').val() || [];
        let selectedPimpinan = $('.duallistbox-pimpinan-rapat').val();
        let selectedNotulis = $('.duallistbox-notulis-rapat').val();

        // Update opsi Pimpinan Rapat
        $('.duallistbox-pimpinan-rapat option').each(function() {
            let val = $(this).val();

            if (val === "") {
                $(this).show().prop('disabled', false);
            } else if (!selectedPeserta.includes(val)) {
                $(this).hide().prop('disabled', false);
            } else if (val === selectedNotulis) {
                $(this).show().prop('disabled', true);
            } else {
                $(this).show().prop('disabled', false);
            }
        });
        duallistPimpinanRapat.bootstrapDualListbox('refresh');

        // Update opsi Notulis Rapat
        $('.duallistbox-notulis-rapat option').each(function() {
            let val = $(this).val();

            if (val === "") {
                $(this).show().prop('disabled', false);
            } else if (!selectedPeserta.includes(val)) {
                $(this).hide().prop('disabled', false);
            } else if (val === selectedPimpinan) {
                $(this).show().prop('disabled', true);
            } else {
                $(this).show().prop('disabled', false);
            }
        });
        duallistNotulisRapat.bootstrapDualListbox('refresh');
    }

    // Jalankan saat halaman load & ketika ada perubahan
    updateOpsiBerdasarkanPeserta();
    $('.duallistbox-peserta-rapat, .duallistbox-pimpinan-rapat, .duallistbox-notulis-rapat')
        .on('change', updateOpsiBerdasarkanPeserta);
</script>
