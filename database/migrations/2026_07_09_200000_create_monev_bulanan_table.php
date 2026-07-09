<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonevBulananTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monev_bulanan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengawas_id');
            $table->unsignedBigInteger('sekolah_id');
            $table->string('bulan', 50);
            $table->integer('tahun');
            
            $table->integer('total_mou')->default(0);
            $table->integer('jumlah_prestasi')->default(0);
            $table->decimal('serapan_bosp', 5, 2)->default(0);
            $table->string('akreditasi', 50)->nullable();
            $table->string('kurikulum', 100)->nullable();
            $table->string('bkk', 50)->nullable();
            $table->string('kondisi_bengkel', 100)->nullable();
            
            $table->integer('siswa_do')->default(0);
            $table->integer('siswa_mutasi')->default(0);
            $table->integer('siswa_pindahan')->default(0);
            
            $table->integer('sarpras_kelas_baik')->default(0);
            $table->integer('sarpras_kelas_rr')->default(0);
            $table->integer('sarpras_kelas_rs')->default(0);
            $table->integer('sarpras_kelas_rb')->default(0);
            
            $table->integer('sarpras_rps_baik')->default(0);
            $table->integer('sarpras_rps_rr')->default(0);
            $table->integer('sarpras_rps_rs')->default(0);
            $table->integer('sarpras_rps_rb')->default(0);
            
            $table->integer('sarpras_lab_baik')->default(0);
            $table->integer('sarpras_lab_rr')->default(0);
            $table->integer('sarpras_lab_rs')->default(0);
            $table->integer('sarpras_lab_rb')->default(0);
            
            $table->integer('sarpras_perpus_baik')->default(0);
            $table->integer('sarpras_perpus_rr')->default(0);
            $table->integer('sarpras_perpus_rs')->default(0);
            $table->integer('sarpras_perpus_rb')->default(0);
            
            $table->integer('mou_kurikulum')->default(0);
            $table->integer('mou_guru')->default(0);
            $table->integer('mou_murid')->default(0);
            $table->integer('mou_sertifikasi')->default(0);
            $table->integer('mou_rekrutmen')->default(0);
            $table->integer('mou_csr')->default(0);
            
            $table->integer('lulusan_kerja')->default(0);
            $table->integer('lulusan_kuliah')->default(0);
            $table->integer('lulusan_wirausaha')->default(0);
            
            $table->decimal('guru_sertifikat', 5, 2)->default(0);
            $table->decimal('guru_non_linier', 5, 2)->default(0);
            
            $table->timestamps();

            // Foreign keys
            $table->foreign('pengawas_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sekolah_id')->references('id')->on('sekolah_m')->onDelete('cascade');

            // Unique constraint to prevent multiple reports per school per month by the same pengawas
            $table->unique(['pengawas_id', 'sekolah_id', 'bulan', 'tahun'], 'monev_unique_report');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monev_bulanan');
    }
}
