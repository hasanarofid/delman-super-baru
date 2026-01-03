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
    protected $fillable = [
        'id_user',
        'id_pelaporan',
        'generate_url',
        'id_pengawas',
        'id_category',
        'submitted_at',
        'id_created_by',
        'id_updated_by',
    ];
    public function pengawasnama()
    {
        return $this->hasOne(User::class, 'id', 'id_pengawas');
    }
    public function user(){
        return $this->hasOne(GuruM::class, 'id', 'id_user');
    }

    public function rencanakerja()
    {
        return $this->hasOne(RencanaKerjaT::class, 'id', 'id_pelaporan');
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
