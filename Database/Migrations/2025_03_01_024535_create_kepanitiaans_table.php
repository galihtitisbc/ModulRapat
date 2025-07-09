<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKepanitiaansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kepanitiaans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pimpinan_id');
            $table->foreign('pimpinan_id')->references('id')->on('pegawais');
            $table->string('nama_kepanitiaan');
            $table->string('access_token');
            $table->string('slug')->unique();
            $table->text('struktur');
            $table->text('deskripsi');
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir');
            $table->string('tujuan');
            $table->enum('status', ['AKTIF', 'NON_AKTIF'])->default('AKTIF');
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
        Schema::table('kepanitiaans', function (Blueprint $table) {
            $table->dropForeign(['pimpinan_id']);
        });
        Schema::dropIfExists('kepanitiaans');
    }
}
