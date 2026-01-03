<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUmpanbalikAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('umpanbalik_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_umpanbalik_t')->constrained('umpanbalik_t')->onDelete('cascade');
            $table->foreignId('id_question')->constrained('umpanbalik_m')->onDelete('cascade');
            $table->text('answer')->nullable();
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
        Schema::dropIfExists('umpanbalik_answers');
    }
}
