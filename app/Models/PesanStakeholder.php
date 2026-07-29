<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Kabupaten;

class PesanStakeholder extends Model
{
    protected $table = 'pesan_stakeholder';

    protected $fillable = [
        'stakeholder_id',
        'kabupaten_id',
        'judul',
        'isi_pesan',
        'is_active',
    ];

    public function stakeholder()
    {
        return $this->belongsTo(User::class, 'stakeholder_id');
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_id');
    }
}
