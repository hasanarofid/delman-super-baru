<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaBlastSetting extends Model
{
    use HasFactory;

    protected $table = 'wa_blast_settings';

    protected $fillable = [
        'reconnect_date',
        'status',
        'day1_3_limit',
        'day4_7_limit',
        'stable_limit',
    ];
}
