<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Rapat\Http\Helper\StatusPesertaRapat;

class RapatPeserta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rapat_pesertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapat_agenda_id')->constrained('rapat_agendas');
            $table->unsignedBigInteger('pegawai_id');
            $table->foreign('pegawai_id')->references('id')->on('pegawais');

            $table->enum('status', [StatusPesertaRapat::BERSEDIA->value, StatusPesertaRapat::TIDAK_BERSEDIA->value, StatusPesertaRapat::HADIR->value, StatusPesertaRapat::TIDAK_HADIR->value, StatusPesertaRapat::MENUNGGU->value])->default(StatusPesertaRapat::MENUNGGU->value);
            $table->boolean('is_penugasan')->default(false);
            $table->text('link_konfirmasi');
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
        Schema::table('rapat_pesertas', function (Blueprint $table) {
            $table->dropForeign(['rapat_agenda_id']);
            $table->dropForeign(['pegawai_id']);
        });

        Schema::dropIfExists('rapat_pesertas');
    }
}
