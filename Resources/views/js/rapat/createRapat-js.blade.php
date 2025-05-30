<script>
    $(document).ready(function() {
        //array untuk menampung data peserta rapat yang dipilih

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        // untuk menentukan waktu selesai akan menggunakan "SELESAI" atau memaasukkan waktu manual
        let waktuSelesai = $("#waktu-selesai");
        let pilihanWaktuSelesai = $("#pilihan-waktu-selesai");
        waktuSelesai.hide();
        pilihanWaktuSelesai.on("change", function() {
            if ($(this).val() == "manual") {
                waktuSelesai.show();
            } else {
                waktuSelesai.hide();
            }
        });

        //untuk menampilkan pilihan tempat rapat, online atau tempat yang lain secara manual
        let tempatRapat = $("#tempat-rapat-group");
        tempatRapat.hide();
        $("#pilihan-tempat").on("change", function() {
            if ($(this).val() == "custom") {
                $("#tempat-rapat").show();
                tempatRapat.show();
            } else {
                $("#tempat-rapat").hide();
                tempatRapat.hide();
            }
        });

        //untuk handle submit form pada form tambah agenda rapat
        $("#form-agenda-rapat").on("submit", function(e) {
            e.preventDefault();

            let pilihanTempat = $("#pilihan-tempat").val();
            const waktuMulai = formatDateTimeLocalToYMDHIS($("#waktu-mulai").val());
            const waktuSelesai =
                pilihanWaktuSelesai.val() == "manual" ?
                formatDateTimeLocalToYMDHIS($("#waktu-selesai").val()) :
                "SELESAI";
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
            //cek apakah pimpinan rapat termasuk dalam daftar peserta rapat
            if (!pesertaRapat.includes(pimpinanRapatUsername)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Pimpinan rapat tidak termasuk dalam daftar peserta rapat.',
                });
                return;
            }
            //cek apakah notulis rapat termasuk dalam daftar peserta rapat
            if (!pesertaRapat.includes(notulisRapatUsername)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Notulis rapat tidak termasuk dalam daftar peserta rapat.',
                });
                return;
            }
            formData.append("pimpinan_username", pimpinanRapatUsername);
            formData.append("notulis_username", notulisRapatUsername);
            formData.append(
                "kepanitiaan_id",
                $('select[name="kepanitiaan_id"]').val()
            );
            $.ajax({
                url: "/rapat/agenda-rapat/store",
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
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
