<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateWaBlastSettingsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('wa_blast_settings')) {
            Schema::create('wa_blast_settings', function (Blueprint $table) {
                $table->id();
                $table->date('reconnect_date')->comment('Tanggal re-connect/unban akun WA');
                $table->string('status', 20)->default('warmup')->comment('warmup / stable');
                $table->integer('day1_3_limit')->default(30)->comment('Limit harian Hari 1-3');
                $table->integer('day4_7_limit')->default(70)->comment('Limit harian Hari 4-7');
                $table->integer('stable_limit')->default(200)->comment('Limit harian mode stabil');
                $table->timestamps();
            });

            // Seed initial row
            DB::table('wa_blast_settings')->insert([
                'reconnect_date' => date('Y-m-d'),
                'status' => 'warmup',
                'day1_3_limit' => 30,
                'day4_7_limit' => 70,
                'stable_limit' => 200,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('wa_blast_settings');
    }
}
