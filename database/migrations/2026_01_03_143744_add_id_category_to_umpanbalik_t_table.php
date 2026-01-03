<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdCategoryToUmpanbalikTTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('umpanbalik_t', function (Blueprint $table) {
            $table->foreignId('id_category')->nullable()->after('id_pengawas')->constrained('umpanbalik_categories')->onDelete('set null');
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
            $table->dropForeign(['id_category']);
            $table->dropColumn('id_category');
        });
    }
}
