<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeNpsnNullableInSekolahMTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Using raw SQL to avoid dependency on doctrine/dbal for modifying columns in Laravel 7
        DB::statement('ALTER TABLE sekolah_m MODIFY npsn VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE sekolah_m MODIFY npsn VARCHAR(255) NOT NULL');
    }
}
