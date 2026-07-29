<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJenjangToPengaturanAspekStakeholderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengaturan_aspek_stakeholder', function (Blueprint $table) {
            $table->string('jenjang', 50)->nullable()->after('pengawas_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengaturan_aspek_stakeholder', function (Blueprint $table) {
            $table->dropColumn('jenjang');
        });
    }
}
