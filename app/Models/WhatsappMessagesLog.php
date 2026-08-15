<?php

namespace App\Models;

use App\GuruM;
use Illuminate\Database\Eloquent\Model;

class WhatsappMessagesLog extends Model
{
    protected $table = 'whatsapp_messages_log';

    protected $guarded = [];

    public function rencanakerja()
    {
        return $this->hasOne(RencanaKerjaT::class, 'id', 'rencana_kerja_id');
    }

    public function kepalasekolah()
    {
        return $this->hasOne(GuruM::class, 'id', 'kepala_sekolah_id');
    }
}
