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
                pesertaRapat = [
                    ...new Set([...pesertaManual, ...pesertaKepanitiaan]),
                ];
                tablePesertaRapat.ajax.reload();
                tablePimpinanRapat.ajax.reload();
                tableNotulisRapat.ajax.reload();
                tableAnggotaPanitia.ajax.reload();
                $('.table-anggota-panitia-group').removeClass('d-none').show();
                $('#peserta-rapat-label').text('Pilih Pegawai Diluar Kepanitiaan ( Jika Ada ) :');
            },
            error: function(xhr) {
                pesertaRapat = [];
                pesertaKepanitiaan = [];
                pesertaRapat = [...pesertaManual];
                tablePesertaRapat.draw();
                tablePimpinanRapat.draw();
                tableNotulisRapat.draw();
                tableAnggotaPanitia.ajax.reload();
                $('.table-anggota-panitia-group').addClass('d-none').hide();
                $('#peserta-rapat-label').text('Pilih Peserta Rapat :');

            },
        });
    });
</script>
