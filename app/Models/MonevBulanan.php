<?php

namespace App\Models;

use App\SekolahM;
use App\User;
use Illuminate\Database\Eloquent\Model;

class MonevBulanan extends Model
{
    protected $table = 'monev_bulanan';

    protected $fillable = [
        'pengawas_id', 'sekolah_id', 'bulan', 'tahun',
        'total_mou', 'jumlah_prestasi', 'serapan_bosp',
        'akreditasi', 'kurikulum', 'bkk', 'kondisi_bengkel',
        'siswa_do', 'siswa_mutasi', 'siswa_pindahan',
        'sarpras_kelas_baik', 'sarpras_kelas_rr', 'sarpras_kelas_rs', 'sarpras_kelas_rb',
        'sarpras_rps_baik', 'sarpras_rps_rr', 'sarpras_rps_rs', 'sarpras_rps_rb',
        'sarpras_lab_baik', 'sarpras_lab_rr', 'sarpras_lab_rs', 'sarpras_lab_rb',
        'sarpras_perpus_baik', 'sarpras_perpus_rr', 'sarpras_perpus_rs', 'sarpras_perpus_rb',
        'mou_kurikulum', 'mou_guru', 'mou_murid', 'mou_sertifikasi', 'mou_rekrutmen', 'mou_csr',
        'lulusan_kerja', 'lulusan_kuliah', 'lulusan_wirausaha',
        'guru_sertifikat', 'guru_non_linier'
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
