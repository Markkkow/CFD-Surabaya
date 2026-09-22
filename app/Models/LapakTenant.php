<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LapakTenant extends Model
{
    protected $table = 'lapak_tenant';
    protected $primaryKey = 'id_lapak';
    public $timestamps = false;

    protected $fillable = [
        'id_event',
        'kategori_lapak',
        'baris',
        'kolom',
        'nomor_lapak',
        'lokasi_lapak',
        'ukuran_lapak',
        'status_lapak'
    ];
}