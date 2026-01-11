<?php

namespace App\Models;

use App\SekolahM;
use App\User;
use Illuminate\Database\Eloquent\Model;

class RencanaKerjaT extends Model
{
    protected $table = 'rencakakerja_t';
    public function pengawasnama()
    {
        return $this->belongsTo(User::class, 'id_pengawas', 'id');
    }

    public function kategoriprogram()
    {
        return $this->belongsTo(Kategory::class, 'kategoriprogram_id', 'id');
    }

    public function jenisprogram()
    {
        return $this->belongsTo(JenisProgram::class, 'jenisprogram_id', 'id');
    }

    public function aspekprogram()
    {
        return $this->belongsTo(AspekProgram::class, 'aspekprogram_id', 'id');
    }

}
