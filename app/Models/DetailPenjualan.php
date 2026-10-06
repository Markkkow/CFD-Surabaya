<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    protected $table = 'detail_penjualan';
    protected $primaryKey = 'id_detail_penjualan';
    public $timestamps = false;

    protected $fillable = [
        'id_laporan',
        'id_produk',
        'jumlah_item_terjual',
        'harga_satuan',
        'subtotal',
    ];

    protected $casts = [
        'jumlah_item_terjual' => 'integer',
        'harga_satuan' => 'integer',
        'subtotal' => 'integer',
    ];

    public function laporan()
    {
        return $this->belongsTo(LaporanPenjualan::class, 'id_laporan', 'id_laporan');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}
