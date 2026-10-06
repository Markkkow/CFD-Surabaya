<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InboxPedagang extends Model
{
    protected $table = 'inbox_pedagang';
    protected $primaryKey = 'id_inbox';

    protected $fillable = [
        'nik_pedagang',
        'tipe',
        'judul',
        'pesan',
        'id_pendaftaran',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function pedagang()
    {
        return $this->belongsTo(Pedagang::class, 'nik_pedagang', 'nik_pedagang');
    }

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranTenant::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }
}
