<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EventCfd extends Model
{
    protected $table = 'event_cfd';
    protected $primaryKey = 'id_event';
    public $timestamps = false;

    protected $fillable = [
        'nama_event', 
        'tanggal_event', 
        'waktu_mulai', 
        'waktu_selesai', 
        'lokasi', 
        'status_event', 
        'tanggal_buka_pendaftaran'
    ];


    /**
     * Event yang masih layak ditampilkan/didaftari.
     * Status harus Aktif dan waktu selesai event belum terlewati.
     */
    public function scopeAktifBerjalan(Builder $query): Builder
    {
        $sekarang = now();

        return $query
            ->where('status_event', 'Aktif')
            ->where(function (Builder $q) use ($sekarang) {
                $q->whereDate('tanggal_event', '>', $sekarang->toDateString())
                    ->orWhere(function (Builder $hariIni) use ($sekarang) {
                        $hariIni->whereDate('tanggal_event', $sekarang->toDateString())
                            ->whereTime('waktu_selesai', '>=', $sekarang->format('H:i:s'));
                    });
            });
    }

    /** Event yang waktu selesainya sudah terlewati. */
    public function scopeSudahSelesai(Builder $query): Builder
    {
        $sekarang = now();

        return $query->where(function (Builder $q) use ($sekarang) {
            $q->whereDate('tanggal_event', '<', $sekarang->toDateString())
                ->orWhere(function (Builder $hariIni) use ($sekarang) {
                    $hariIni->whereDate('tanggal_event', $sekarang->toDateString())
                        ->whereTime('waktu_selesai', '<', $sekarang->format('H:i:s'));
                });
        });
    }

    public function lapak()
    {
        return $this->hasMany(LapakTenant::class, 'id_event', 'id_event');
    }

    public function pendaftaran()
    {
        return $this->hasMany(PendaftaranTenant::class, 'id_event', 'id_event');
    }
}