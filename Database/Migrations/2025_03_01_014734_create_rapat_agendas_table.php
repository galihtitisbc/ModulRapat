<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Rapat\Http\Helper\StatusAgendaRapat;

class CreateRapatAgendasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rapat_agendas', function (Blueprint $table) {
            $table->id();
            $table->string('pegawai_username');
            $table->foreign('pegawai_username')->references('username')->on('pegawais');

            $table->string('pimpinan_username');
            $table->foreign('pimpinan_username')->references('username')->on('pegawais');

            $table->string('notulis_username');
            $table->foreign('notulis_username')->references('username')->on('pegawais');

            $table->string('nomor_surat');
            $table->text('slug')->nullable();
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai')->nullable();
            $table->text('agenda_rapat');
            $table->string('tempat');
            $table->enum('status', [StatusAgendaRapat::STARTED->value, StatusAgendaRapat::CANCELLED->value, StatusAgendaRapat::COMPLETED->value, StatusAgendaRapat::SCHEDULED->value])->default(StatusAgendaRapat::SCHEDULED->value);
            $table->string('lampiran')->nullable();
            $table->text('zoom_link')->nullable();
            $table->text('calendar_link')->nullable();
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
        Schema::dropIfExists('rapat_agendas');
    }
}
