<script>
    const BASE_URL = "{{ env('APP_URL') }}";

    //peserta yang dipilih oleh pembuat rapat pada duallist
    let pesertaManual = [];
    //peserta yang berasal dari kepanitiaan
    let pesertaKepanitiaan = [];

    //untuk menentukan apakah sedang update atau tidak
    let isUpdate = false;
    let isFirstLoad = false;
    //digunakan untuk menampung data peserta kepanitiaan yang dipilih
    let pesertaRapat = [];
    let pimpinanKepanitiaan = "";

    //function untuk mencegah ajax reload berkali kali
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    const reloadTable = debounce(() => {
        if (typeof tablePesertaPanitia !== "undefined" && tablePesertaPanitia !== null) {
            tablePesertaPanitia.ajax.reload();
        }

        if (typeof tableStrukturKepanitiaan !== "undefined" && tableStrukturKepanitiaan !== null) {
            tableStrukturKepanitiaan.ajax.reload();
        }
    }, 300);
</script>
