<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhatsappMessagesLogTable extends Migration
{
    public function up()
    {
        return;
        Schema::create('whatsapp_messages_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rencana_kerja_id')->constrained('rencakakerja_t')->onDelete('cascade');
            $table->foreignId('kepala_sekolah_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('phone_number', 20); // Phone number where the message was sent
            $table->text('message'); // The content of the WhatsApp message
            $table->boolean('is_sent')->default(false); // Status of the message
            $table->string('failure_reason')->nullable(); // Reason if not sent
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('whatsapp_messages_log');
    }
}
