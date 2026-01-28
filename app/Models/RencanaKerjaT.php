<?php

namespace App\Models;

use App\SekolahM;
use App\User;
use Illuminate\Database\Eloquent\Model;

class RencanaKerjaT extends Model
{
    protected $table = 'rencakakerja_t';
    protected $fillable = [
        'id_pengawas',
        'nama_program_kerja',
        'kategoriprogram_id',
        'jenisprogram_id',
        'aspekprogram_id',
        'bulan',
        'tahun_ajaran',
        'sekolah_id',
        'deskripsi_permasalahan',
        'target_capaian',
        'tenggat_waktu',
        'id_umpanbalik_category',
        'is_mandiri',
        'status'
    ];
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
