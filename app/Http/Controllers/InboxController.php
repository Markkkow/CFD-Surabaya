<?php

namespace App\Http\Controllers;

use App\Models\InboxPedagang;
use Illuminate\Support\Facades\Auth;

class InboxController extends Controller
{
    public function index()
    {
        $pedagang = Auth::guard('pedagang')->user();

        $messages = InboxPedagang::where('nik_pedagang', $pedagang->nik_pedagang)
            ->orderByDesc('created_at')
            ->orderByDesc('id_inbox')
            ->paginate(10);

        return view('inbox.index', compact('messages'));
    }

    public function read(InboxPedagang $inbox)
    {
        $pedagang = Auth::guard('pedagang')->user();

        abort_unless($inbox->nik_pedagang === $pedagang->nik_pedagang, 403);

        if (is_null($inbox->read_at)) {
            $inbox->update(['read_at' => now()]);
        }

        return back();
    }

    public function readAll()
    {
        $pedagang = Auth::guard('pedagang')->user();

        InboxPedagang::where('nik_pedagang', $pedagang->nik_pedagang)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Semua pesan telah ditandai sebagai sudah dibaca.');
    }
}
