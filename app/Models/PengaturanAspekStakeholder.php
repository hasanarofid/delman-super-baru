<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Kabupaten;

class PengaturanAspekStakeholder extends Model
{
    protected $table = 'pengaturan_aspek_stakeholder';

    protected $fillable = [
        'stakeholder_id',
        'kabupaten_id',
        'pengawas_id',
        'jenjang',
        'aspek_program_id',
        'is_active',
        'bulan',
        'tahun',
    ];

    public function stakeholder()
    {
        return $this->belongsTo(User::class, 'stakeholder_id');
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_id');
    }

    public function pengawas()
    {
        return $this->belongsTo(User::class, 'pengawas_id');
    }

    public function aspekProgram()
    {
        return $this->belongsTo(AspekProgram::class, 'aspek_program_id');
    }
}
