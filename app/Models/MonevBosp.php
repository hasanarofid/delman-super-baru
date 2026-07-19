<?php

namespace App\Models;

use App\SekolahM;
use App\User;
use Illuminate\Database\Eloquent\Model;

class MonevBosp extends Model
{
    protected $table = 'monev_bosp';

    protected $fillable = [
        'pengawas_id', 'sekolah_id', 'bulan', 'tahun',
        'status_ijop', 'siswa_kelas_10', 'siswa_kelas_11', 'siswa_kelas_12',
        'total_siswa_riil', 'siswa_dinas_bos', 'realisasi_bosp',
        'catatan_observasi', 'file_sptjm'
    ];

    public function pengawas()
    {
        return $this->belongsTo(User::class, 'pengawas_id');
    }

    public function sekolah()
    {
        return $this->belongsTo(SekolahM::class, 'sekolah_id');
    }
}
