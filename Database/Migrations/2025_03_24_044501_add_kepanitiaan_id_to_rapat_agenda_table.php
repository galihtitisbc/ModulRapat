<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKepanitiaanIdToRapatAgendaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rapat_agendas', function (Blueprint $table) {
            $table->foreignId('kepanitiaan_id')->nullable()->constrained('kepanitiaans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rapat_agendas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kepanitiaan_id');
        });
    }
}
