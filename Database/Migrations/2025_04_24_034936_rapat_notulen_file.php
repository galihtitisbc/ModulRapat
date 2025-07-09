<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RapatNotulenFile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rapat_notulen_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapat_notulen_id')->constrained('rapat_notulens');
            $table->string('nama_file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rapat_notulen_files', function (Blueprint $table) {
            $table->dropForeign(['rapat_notulen_id']);
        });
        Schema::dropIfExists('rapat_notulen_files');
    }
}
