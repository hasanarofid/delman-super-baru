<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmpanbalikAnswer extends Model
{
    protected $table = 'umpanbalik_answers';

    protected $fillable = [
        'id_umpanbalik_t',
        'id_question',
        'answer',
    ];

    public function umpanbalikT()
    {
        return $this->belongsTo(UmpanbalikT::class, 'id_umpanbalik_t');
    }

    public function question()
    {
        return $this->belongsTo(UmpanbalikM::class, 'id_question');
    }
}

