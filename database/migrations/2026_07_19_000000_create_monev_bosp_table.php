<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonevBospTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monev_bosp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengawas_id');
            $table->unsignedBigInteger('sekolah_id');
            $table->string('bulan', 20);
            $table->integer('tahun');
            $table->string('status_ijop', 100)->nullable();
            
            $table->integer('siswa_kelas_10')->default(0);
            $table->integer('siswa_kelas_11')->default(0);
            $table->integer('siswa_kelas_12')->default(0);
            $table->integer('total_siswa_riil')->default(0);
            $table->integer('siswa_dinas_bos')->default(0);
            
            $table->decimal('realisasi_bosp', 15, 2)->default(0);
            $table->text('catatan_observasi')->nullable();
            $table->string('file_sptjm', 255)->nullable();
            
            $table->timestamps();

            // Foreign keys
            $table->foreign('pengawas_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sekolah_id')->references('id')->on('sekolah_m')->onDelete('cascade');

            // Unique constraint to prevent multiple reports per school per month by the same pengawas
            $table->unique(['pengawas_id', 'sekolah_id', 'bulan', 'tahun'], 'monev_bosp_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monev_bosp');
    }
}
