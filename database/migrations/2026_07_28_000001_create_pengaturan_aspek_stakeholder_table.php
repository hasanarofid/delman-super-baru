<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengaturanAspekStakeholderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengaturan_aspek_stakeholder', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stakeholder_id')->nullable();
            $table->unsignedBigInteger('kabupaten_id')->nullable();
            $table->unsignedBigInteger('pengawas_id')->nullable();
            $table->unsignedBigInteger('aspek_program_id');
            $table->boolean('is_active')->default(1);
            $table->unsignedTinyInteger('bulan')->nullable(); // 1-12
            $table->unsignedSmallInteger('tahun')->nullable(); // 2026, dll
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
        Schema::dropIfExists('pengaturan_aspek_stakeholder');
    }
}
