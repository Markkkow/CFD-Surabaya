<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perizinan extends Model
{
    protected $table = 'perizinan';
    protected $primaryKey = 'id_perizinan';
    public $timestamps = false;

    protected $fillable = [
        'nik_pedagang',
        'id_event',
        'jenis_perizinan',
        'tanggal_pengajuan',
        'tanggal_berlaku',
        'status_perizinan',
        'dokumen_perizinan',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_berlaku' => 'date',
    ];

    public function pedagang()
    {
        return $this->belongsTo(Pedagang::class, 'nik_pedagang', 'nik_pedagang');
    }

    public function event()
    {
        return $this->belongsTo(EventCfd::class, 'id_event', 'id_event');
    }
}
