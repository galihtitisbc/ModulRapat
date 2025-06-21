<script>
    $(document).ready(function() {
        //declare variable
        let selectWaktuSelesai = $("#pilihan-waktu-selesai");
        let waktuSelesai = $("#waktu-selesai");
        let selectPilihanTempat = $("#pilihan-tempat");
        let tempatRapat = $('input[name="tempat_rapat"]');
        let tempatRapatGroup = $("#tempat-rapat-group");
        selectWaktuSelesai.on("change", function() {
            if ($(this).val() == "selesai" || $(this).val() == "") {
                waktuSelesai.hide();
            } else {
                waktuSelesai.show();
            }
        });
        $("#pilihan-tempat").on("change", function() {
            if ($(this).val() == "custom") {
                $("#tempat-rapat").show();
                tempatRapat.show();
            } else {
                $("#tempat-rapat").hide();
                tempatRapat.hide();
            }
        });
        //untuk menampilkan pilihan tempat rapat, online atau tempat yang lain secara manual
        tempatRapatGroup.hide();
        tempatRapat.hide();
        waktuSelesai.hide();

        // untuk menampilkan data form edit dari controller, dengan menggunakan <?php echo json_encode($rapatAgenda); ?>;
        // diblade view
        $('input[name="nomor_surat"]').val(rapat.nomor_surat);
        $('input[name="waktu_mulai"]').val(rapat.waktu_mulai);
        $("#agenda-rapat").val(rapat.agenda_rapat);

        if (rapat.waktu_selesai !== null) {
            waktuSelesai.show();
            selectWaktuSelesai.val("manual");
            $('input[name="waktu_selesai"]').val(rapat.waktu_selesai);
        } else {
            selectWaktuSelesai.val("selesai");
        }
        if (rapat.tempat === "zoom") {
            selectPilihanTempat.val("zoom");
        }
        if (rapat.tempat !== "zoom") {
            tempatRapatGroup.show();
            selectPilihanTempat.val("custom");
            tempatRapat.val(rapat.tempat);
            tempatRapat.show();
        }
        if (rapat.kepanitiaan_id !== null) {
            $("#kepanitiaan").val(rapat.kepanitiaan_id);
        }
        if (rapat.rapat_kepanitiaan !== null) {
            pesertaKepanitiaan = rapat.rapat_kepanitiaan.pegawai.map(
                (pegawai) => pegawai.username
            );
            lastPesertaKepanitiaan = [...pesertaKepanitiaan];

        }
        /////////////////////////////////

        //request ajax untuk submit form
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $("#form-update-rapat").submit(function(e) {
            e.preventDefault();
            let pilihanTempat = $("#pilihan-tempat").val();
            waktuMulai = formatDateTimeLocalToYMDHIS($("#waktu-mulai").val());
            waktuSelesai =
                selectWaktuSelesai.val() == "manual" ?
                formatDateTimeLocalToYMDHIS($("#waktu-selesai").val()) :
                "SELESAI";
            // Ambil semua value (username) dari opsi yang terpilih
            let pesertaRapat = $('.duallistbox-peserta-rapat').val() || [];
            let pimpinanRapat = $('.duallistbox-pimpinan-rapat').val();
            let notulisRapat = $('.duallistbox-notulis-rapat').val();

            let formData = new FormData(this);

            let tempat =
                pilihanTempat === "zoom" ?
                "zoom" :
                $('input[name="tempat_rapat"]').val();

            formData.append("nomor_surat", $('input[name="nomor_surat"]').val());
            formData.append("waktu_mulai", waktuMulai);
            formData.append("waktu_selesai", waktuSelesai);
            formData.append("tempat", tempat);
            formData.append(
                "agenda_rapat",
                $('textarea[name="agenda_rapat"]').val()
            );
            pesertaRapat.forEach((username) => {
                formData.append("peserta_rapat[]", username);
            });
            if (!pesertaRapat.length) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Minimal satu peserta harus dipilih',
                });
                return;
            }
            //cek apakah pimpinan rapat termasuk dalam daftar peserta rapat
            if (!pesertaRapat.includes(pimpinanRapat) && pimpinanRapat !== null) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Pimpinan rapat tidak termasuk dalam daftar peserta rapat.',
                });
                return;
            }
            //cek apakah notulis rapat termasuk dalam daftar peserta rapat
            if (!pesertaRapat.includes(notulisRapat) && notulisRapat !== null) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Notulis rapat tidak termasuk dalam daftar peserta rapat.',
                });
                return;
            }
            formData.append("pimpinan_username", pimpinanRapat);
            formData.append("notulis_username", notulisRapat);
            formData.append(
                "kepanitiaan_id",
                $('select[name="kepanitiaan_id"]').val()
            );
            $.ajax({
                url: `/rapat/agenda-rapat/${slug}/update`,
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    "X-HTTP-Method-Override": "PUT",
                },
                success: function(response) {
                    $(".invalid-feedback").text("");
                    $("input, select, textarea").removeClass("is-invalid");
                    $("#form-errors-list").hide();
                    Swal.fire({
                        title: `${response.title}`,
                        text: `${response.message}`,
                        icon: `${response.icon}`,
                    });
                    if (response.success == true) {
                        setTimeout(() => {
                            window.location.href = "/rapat/agenda-rapat";
                        }, 1500);
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;

                    $(".invalid-feedback").text("");
                    $("input, select, textarea").removeClass("is-invalid");

                    Object.keys(errors).forEach(function(key) {
                        let field = key.replace(".", "_");
                        $(`#error-${field}`).text(errors[key][0]);
                        $(`[name="${key}"]`).addClass("is-invalid");
                    });
                    // Reset dan tampilkan container error
                    $("#form-errors").removeClass("d-none");
                    $("#form-errors-list").html("");

                    // Loop semua pesan error dan tampilkan dalam list
                    Object.keys(errors).forEach(function(key) {
                        errors[key].forEach(function(message) {
                            $("#form-errors-list").append(
                                `<li>${message}</li>`);
                        });
                    });

                    // Optional: scroll ke atas ke pesan error
                    $("html, body").animate({
                            scrollTop: $("#form-errors").offset().top - 100,
                        },
                        500
                    );
                },
            });
        });
    });

    function formatDateTimeLocalToYMDHIS(value) {
        if (!value) return "";
        const [date, time] = value.split("T");
        return `${date} ${time}:00`;
    }
</script>
