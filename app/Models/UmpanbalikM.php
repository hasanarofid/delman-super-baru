<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmpanbalikM extends Model
{
    protected $table = 'umpanbalik_m';

    protected $fillable = [
        'id_category',
        'pertanyaan',
        'type_input',
        'options',
        'status',
        'urutan',
    ];

    protected $casts = [
        'options' => 'array',
        'status' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(UmpanbalikCategory::class, 'id_category');
    }
}
