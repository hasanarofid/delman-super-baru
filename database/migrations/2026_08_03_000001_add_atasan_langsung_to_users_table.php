<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAtasanLangsungToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama_atasan')->nullable()->after('akses_jenjang');
            $table->string('nip_atasan')->nullable()->after('nama_atasan');
            $table->string('jabatan_atasan')->nullable()->after('nip_atasan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nama_atasan', 'nip_atasan', 'jabatan_atasan']);
        });
    }
}
