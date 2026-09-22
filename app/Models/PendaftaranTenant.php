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
        'nama_produk',
        'jenis_produk',
        'jumlah_produk',
        'keterangan_tambahan',
        'foto_produk',
        'tanggal_pendaftaran',
        'status_pendaftaran',
        'catatan_admin',
    ];

    /** Status yang dianggap masih "aktif" / memblokir pedagang memilih lapak lain di event yang sama. */
    public const STATUS_AKTIF = ['Menunggu Data Produk', 'Menunggu Verifikasi', 'Terverifikasi'];

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

    public function laporanPenjualan()
    {
        return $this->hasOne(LaporanPenjualan::class, 'id_pendaftaran', 'id_pendaftaran');
    }
}
