<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanPenjualan extends Model
{
    protected $table = 'laporan_penjualan';
    protected $primaryKey = 'id_laporan';
    public $timestamps = false;

    protected $fillable = [
        'id_pendaftaran',
        'nik_pedagang',
        'id_event',
        'total_omzet',
        'jumlah_terjual',
        'total_pendapatan',
        'total_modal',
        'catatan_penjualan',
        'tanggal_laporan',
        'updated_at',
    ];

    protected $casts = [
        'jumlah_terjual' => 'integer',
        'total_pendapatan' => 'decimal:2',
        'total_omzet' => 'decimal:2',
        'total_modal' => 'decimal:2',
        'tanggal_laporan' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranTenant::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    public function getLabaAttribute(): float
    {
        return (float) $this->total_pendapatan - (float) $this->total_modal;
    }
}
