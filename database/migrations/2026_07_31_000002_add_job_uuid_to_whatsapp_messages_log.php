<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJobUuidToWhatsappMessagesLog extends Migration
{
    public function up()
    {
        Schema::table('whatsapp_messages_log', function (Blueprint $table) {
            if (!Schema::hasColumn('whatsapp_messages_log', 'job_uuid')) {
                $table->string('job_uuid')->nullable()->after('failure_reason');
            }
        });
    }

    public function down()
    {
        Schema::table('whatsapp_messages_log', function (Blueprint $table) {
            $table->dropColumn('job_uuid');
        });
    }
}
