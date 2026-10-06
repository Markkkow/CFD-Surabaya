<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Pedagang extends Authenticatable
{
    protected $table = 'pedagang';
    protected $primaryKey = 'nik_pedagang';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

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

    public function produk()
    {
        return $this->hasMany(Produk::class, 'nik_pedagang', 'nik_pedagang');
    }

    public function perizinan()
    {
        return $this->hasMany(Perizinan::class, 'nik_pedagang', 'nik_pedagang');
    }

    public function inbox()
    {
        return $this->hasMany(InboxPedagang::class, 'nik_pedagang', 'nik_pedagang');
    }

    public function unreadInbox()
    {
        return $this->hasMany(InboxPedagang::class, 'nik_pedagang', 'nik_pedagang')
            ->whereNull('read_at');
    }
}