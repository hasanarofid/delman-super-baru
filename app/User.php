<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'nip', 'jenjang_jabatan', 'pangkat', 'gol_ruang',
        'foto_profile', 'role', 'kabupaten_id', 'no_telp', 'kota', 'alamat_lengkap', 'kode_area'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeFindByEmailOrNip($query, $identifier)
{
    return $query->where('email', $identifier)
                 ->orWhere('nip', $identifier);
}

    public static function boot()
    {
        parent::boot();
        static::created(function ($user){
            $user->profile()->create();
        });
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

      public function kabupaten()
    {
        return $this->hasOne(Kabupaten::class, 'id', 'kabupaten_id');
    }

   
}