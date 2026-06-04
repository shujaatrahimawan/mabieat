<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoteFieldsToPosSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_settings', function (Blueprint $table) {
            $table->text('note_retail')->nullable();
            $table->text('note_wholesale')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pos_settings', function (Blueprint $table) {
            $table->dropColumn('note_retail');
            $table->dropColumn('note_wholesale');
        });
    }
}
