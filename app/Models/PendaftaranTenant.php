<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftaranTenant extends Model
{
    protected $table = 'pendaftaran_tenant';
    protected $primaryKey = 'id_pendaftaran';
    public $timestamps = false;

    protected $fillable = [
        'nik_pedagang',
        'id_event',
        'id_lapak',
        'id_produk',
        'nama_produk',
        'jenis_produk',
        'jumlah_produk',
        'keterangan_tambahan',
        'foto_produk',
        'tanggal_pendaftaran',
        'status_pendaftaran',
        'catatan_admin',
    ];

    public const STATUS_AKTIF = ['Menunggu Data Produk', 'Menunggu Perizinan', 'Menunggu Verifikasi', 'Terverifikasi'];

    public function pedagang()
    {
        return $this->belongsTo(Pedagang::class, 'nik_pedagang', 'nik_pedagang');
    }

    public function event()
    {
        return $this->belongsTo(EventCfd::class, 'id_event', 'id_event');
    }

    public function lapak()
    {
        return $this->belongsTo(LapakTenant::class, 'id_lapak', 'id_lapak');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function laporanPenjualan()
    {
        return $this->hasOne(LaporanPenjualan::class, 'id_pendaftaran', 'id_pendaftaran');
    }
}
