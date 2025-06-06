<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class KepanitiaanPegawai extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kepanitiaan_pegawai', function (Blueprint $table) {
            $table->foreignId('kepanitiaan_id')->constrained('kepanitiaans');
            $table->string('pegawai_username');
            $table->foreign('pegawai_username')->references('username')->on('pegawais');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
