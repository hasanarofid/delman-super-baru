<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryAndOptionsToUmpanbalikMTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('umpanbalik_m', function (Blueprint $table) {
            $table->foreignId('id_category')->nullable()->after('id')->constrained('umpanbalik_categories')->onDelete('set null');
            $table->json('options')->nullable()->after('type_input');
            $table->dropColumn('jawaban');
            // Jika kolom 'aspek' masih ada dan ingin dihapus/diubah, uncomment baris ini:
            // $table->dropColumn('aspek');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('umpanbalik_m', function (Blueprint $table) {
            $table->text('jawaban')->nullable()->after('pertanyaan');
            $table->dropColumn('options');
            $table->dropForeign(['id_category']);
            $table->dropColumn('id_category');
            // Jika kolom 'aspek' dihapus di 'up', tambahkan kembali di sini:
            // $table->string('aspek')->nullable()->after('type_input');
        });
    }
}
