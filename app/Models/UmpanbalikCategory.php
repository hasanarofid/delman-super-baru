<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmpanbalikCategory extends Model
{
    protected $table = 'umpanbalik_categories';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    public function questions()
    {
        return $this->hasMany(UmpanbalikM::class, 'id_category');
    }
}

