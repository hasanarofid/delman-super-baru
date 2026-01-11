<?php

namespace App\Models;

use App\User;
use App\GuruM;
use App\TanggapanUmpanbalikT;
use App\Models\RencanaKerjaT;
use App\Models\UmpanbalikAnswer;
use Illuminate\Database\Eloquent\Model;

class UmpanbalikT extends Model
{
    protected $table = 'umpanbalik_t';
    protected $dates = ['submitted_at', 'tgl_rtl', 'tgl_pendampingan'];
    protected $fillable = [
        'id_user',
        'id_pelaporan',
        'generate_url',
        'id_pengawas',
        'id_category',
        'submitted_at',
        'id_created_by',
        'id_updated_by',
        'tgl_rtl',
        'tgl_pendampingan',
    ];
    public function pengawasnama()
    {
        return $this->belongsTo(User::class, 'id_pengawas', 'id');
    }
    public function user(){
        return $this->belongsTo(GuruM::class, 'id_user', 'id');
    }

    public function rencanakerja()
    {
        return $this->belongsTo(RencanaKerjaT::class, 'id_pelaporan', 'id');
    }

    public function tanggapanUmpanBalik()
    {
        return $this->hasMany(TanggapanUmpanbalikT::class, 'id_umpanbalik');
    }

    public function answers()
    {
        return $this->hasMany(UmpanbalikAnswer::class, 'id_umpanbalik_t');
    }

    public function getAnswerForQuestion($questionId)
    {
        $answer = $this->answers()->where('id_question', $questionId)->first();
        return $answer ? $answer->answer : null;
    }

    public function category()
    {
        return $this->belongsTo(UmpanbalikCategory::class, 'id_category');
    }
}
