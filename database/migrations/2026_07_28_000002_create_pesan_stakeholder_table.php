<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePesanStakeholderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pesan_stakeholder', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stakeholder_id')->nullable();
            $table->unsignedBigInteger('kabupaten_id')->nullable();
            $table->string('judul')->default('Pesan Stakeholder');
            $table->text('isi_pesan');
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('pesan_stakeholder');
    }
}
