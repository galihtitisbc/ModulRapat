<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRapatTindakLanjutFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rapat_tindak_lanjut_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapat_tindak_lanjut_id')->constrained('rapat_tindak_lanjuts');
            $table->string('nama_file');
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
        Schema::table('rapat_tindak_lanjut_files', function (Blueprint $table) {
            $table->dropForeign(['rapat_tindak_lanjut_id']);
        });
        Schema::dropIfExists('rapat_tindak_lanjut_files');
    }
}
