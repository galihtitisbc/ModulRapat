<script>
    let pesertaManual = [];
    let pesertaKepanitiaan = [];
    let pesertaRapat = [];
    let pimpinanRapatUsername = "";
    let notulisRapatUsername = "";
    let pimpinanKepanitiaan = "";

    //function untuk mencegah ajax reload berkali kali
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }
    //daftar semua table, table akan di reload jika memenuhi kondisi if
    const reloadSemuaTable = debounce(() => {
        if (typeof tablePesertaRapat !== "undefined" && tablePesertaRapat !== null) {
            tablePesertaRapat.ajax.reload();
        }
        if (typeof tablePimpinanRapat !== "undefined" && tablePimpinanRapat !== null) {
            tablePimpinanRapat.ajax.reload();
        }
        if (typeof tableNotulisRapat !== "undefined" && tableNotulisRapat !== null) {
            tableNotulisRapat.ajax.reload();
        }
        if (typeof tableStrukturKepanitiaan !== "undefined" && tableStrukturKepanitiaan !== null) {
            tableStrukturKepanitiaan.ajax.reload();
        }
    }, 300);
</script>
