<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GuruM extends Model
{
        protected $table = 'guru_m';

        protected $fillable = [
            'sekolah_id',
            'user_id',
            'nama',
            'nip',
            'jenis_kelamin',
            'tempat_lahir',
            'tanggal_lahir',
            'agama',
            'alamat',
            'no_hp',
            'no_telp',
            'email',
            'status',
            'kabupaten_id',
            'jabatan',
            'is_aktif',
        ];

          public function sekolah()
    {
        return $this->hasOne(SekolahM::class, 'id', 'sekolah_id');
    }

    public function kabupaten()
    {
        return $this->hasOne(Kabupaten::class, 'id', 'kabupaten_id');
    }



}
