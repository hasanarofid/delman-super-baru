<?php

namespace App\Models;

use App\SekolahM;
use App\User;
use Illuminate\Database\Eloquent\Model;

class SekolahbinaanT extends Model
{
    protected $table = 'sekolahbinaan_t';
    
    protected $fillable = [
        'id_pengawas',
        'id_sekolah',
    ];
    
    public function sekolah()
    {
        return $this->hasOne(SekolahM::class, 'id', 'id_sekolah');

    }

    public function pengawas()
    {
        return $this->hasOne(User::class, 'id', 'id_pengawas');

    }
}
