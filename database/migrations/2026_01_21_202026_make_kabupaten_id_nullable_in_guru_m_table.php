<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeKabupatenIdNullableInGuruMTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use raw SQL to avoid Doctrine DBAL dependency
        \DB::statement('ALTER TABLE `guru_m` MODIFY `kabupaten_id` INT(11) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guru_m', function (Blueprint $table) {
            // Revert if necessary - make it non-nullable again
            // $table->integer('kabupaten_id')->nullable(false)->change();
        });
    }
}
