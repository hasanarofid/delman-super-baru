<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTglPendampinganToUmpanbalikTTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('umpanbalik_t', function (Blueprint $table) {
            $table->date('tgl_pendampingan')->nullable()->after('id_category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('umpanbalik_t', function (Blueprint $table) {
            $table->dropColumn('tgl_pendampingan');
        });
    }
}
