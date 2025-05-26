<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
        Schema::dropIfExists('rapat_tindak_lanjut_files');
    }
}
