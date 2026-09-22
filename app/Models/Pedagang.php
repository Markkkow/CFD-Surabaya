<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Pedagang extends Authenticatable
{
    protected $table = 'pedagang';
    protected $primaryKey = 'nik_pedagang';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // Karena di skema SQL tidak ada created_at/updated_at

    protected $fillable = [
        'nik_pedagang', 'nama_pedagang', 'nama_usaha', 'sosial_media_usaha', 
        'no_telepon', 'email', 'alamat', 'ktp_pedagang', 'username', 'password', 'status_verifikasi'
    ];

    protected $hidden = [
        'password',
    ];

    public function pendaftaran()
    {
        return $this->hasMany(PendaftaranTenant::class, 'nik_pedagang', 'nik_pedagang');
    }
}