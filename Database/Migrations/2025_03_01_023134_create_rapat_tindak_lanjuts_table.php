<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Rapat\Http\Helper\KriteriaPenilaian;
use Modules\Rapat\Http\Helper\StatusTindakLanjut;

class CreateRapatTindakLanjutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rapat_tindak_lanjuts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapat_agenda_id')->constrained('rapat_agendas');
            $table->string('pegawai_username');
            $table->foreign('pegawai_username')->references('username')->on('pegawais');
            $table->text('deskripsi_tugas');
            $table->text('slug');
            $table->date('batas_waktu');
            $table->enum('status', [StatusTindakLanjut::BELUM_SELESAI->value, StatusTindakLanjut::SELESAI->value])->default(StatusTindakLanjut::BELUM_SELESAI->value);
            $table->string('tugas')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();
            // $table->enum('penilaian', ['MELEBIHI EKSPETASI', 'SESUAI EKSPETASI', "TIDAK SESUAI EKSPETASI", "BELUM DINILAI"])->default("BELUM DINILAI");
            $table->enum('penilaian', [KriteriaPenilaian::BELUM_DINILAI->value, KriteriaPenilaian::MELEBIHI_EKSPETASI->value, KriteriaPenilaian::SESUAI_EKSPETASI->value, KriteriaPenilaian::TIDAK_SESUAI_EKSPETASI->value])->default(KriteriaPenilaian::BELUM_DINILAI->value);
            $table->text('kendala')->nullable();
            $table->text('komentar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rapat_tindak_lanjuts');
    }
}
