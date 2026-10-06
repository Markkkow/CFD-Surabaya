<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    public $timestamps = false;

    protected $fillable = [
        'nik_pedagang',
        'nama_produk',
        'kategori_produk',
        'harga',
        'stok_produk',
        'deskripsi_produk',
        'foto_produk',
    ];

    protected $casts = [
        'harga' => 'integer',
        'stok_produk' => 'integer',
    ];

    public function pedagang()
    {
        return $this->belongsTo(Pedagang::class, 'nik_pedagang', 'nik_pedagang');
    }

    public function pendaftaran()
    {
        return $this->hasMany(PendaftaranTenant::class, 'id_produk', 'id_produk');
    }

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'id_produk', 'id_produk');
    }
}
