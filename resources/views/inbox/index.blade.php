@extends('layouts.app')

@section('title', 'Inbox - CFD Surabaya')

@push('styles')
<style>
    .inbox-header {
        box-shadow: 0 10px 30px rgba(20,42,32,.08);
        border: 1px solid rgba(255,255,255,.18);
        background: linear-gradient(135deg, var(--cfd-green), var(--cfd-green-dark));
        color: #fff;
        border-radius: 1.25rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .inbox-card {
        position: relative;
        background: #fff;
        border: 1px solid #e9eeeb;
        border-radius: 1rem;
        padding: 1.15rem;
        margin-bottom: .85rem;
        transition: .18s ease;
    }
    .inbox-card:hover { transform: translateY(-1px); box-shadow: 0 .5rem 1.25rem rgba(30,42,38,.07); }
    .inbox-card.unread { border-left: 1px solid #e2eee7; background: #fbfffc; }
    .inbox-icon { width: 46px; height: 46px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.25rem; }
    .inbox-icon.accepted { background: #dff3e6; color: var(--cfd-green-dark); }
    .inbox-icon.rejected { background: #fde2e2; color: #a3282c; }
    .inbox-message { white-space: pre-line; color: #5f6c66; line-height: 1.65; }
    .inbox-card.unread::before { content:''; position:absolute; left:0; top:16px; bottom:16px; width:3px; border-radius:0 4px 4px 0; background:var(--cfd-green); }
    .inbox-card .btn { border-radius:9px; }
    .unread-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--cfd-green); display: inline-block; }
</style>
@endpush

@section('content')
<div class="inbox-header shadow-sm">
    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-inbox-fill fs-3"></i>
                <h2 class="fw-heading fw-bold mb-0">Inbox</h2>
            </div>
            <div class="opacity-75">Informasi terbaru mengenai pendaftaran dan verifikasi kamu.</div>
        </div>
        @if($messages->total() > 0)
            <form action="{{ route('inbox.readAll') }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-light fw-semibold">
                    <i class="bi bi-check2-all me-1"></i>Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>
</div>

@if($messages->isEmpty())
    <div class="bg-white border rounded-4 shadow-sm text-center py-5 px-3">
        <i class="bi bi-inbox fs-1 text-muted"></i>
        <h5 class="fw-bold mt-3 mb-2">Inbox masih kosong</h5>
        <p class="text-muted mb-0">Belum ada pemberitahuan untuk kamu.</p>
    </div>
@else
    @foreach($messages as $message)
        @php
            $isAccepted = $message->tipe === 'diterima';
            // Bersihkan pesan lama yang tersimpan dengan escape literal.
            $displayMessage = str_replace(
                ['\\"', '\\r\\n', '\\n', '\\r'],
                ['"', "\n", "\n", "\n"],
                $message->pesan
            );
        @endphp
        <div class="inbox-card {{ is_null($message->read_at) ? 'unread' : '' }}">
            <div class="d-flex gap-3">
                <div class="inbox-icon {{ $isAccepted ? 'accepted' : 'rejected' }}">
                    <i class="bi {{ $isAccepted ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                @if(is_null($message->read_at)) <span class="unread-dot"></span> @endif
                                <h5 class="fw-bold mb-0">{{ $message->judul }}</h5>
                            </div>
                            <div class="small text-muted mt-1">
                                <i class="bi bi-clock me-1"></i>{{ $message->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                        @if(is_null($message->read_at))
                            <form action="{{ route('inbox.read', $message) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-check2 me-1"></i>Tandai Dibaca
                                </button>
                            </form>
                        @else
                            <span class="badge rounded-pill bg-light text-secondary border">Sudah dibaca</span>
                        @endif
                    </div>
                    <div class="inbox-message mt-3">{{ $displayMessage }}</div>
                </div>
            </div>
        </div>
    @endforeach
    @if($messages->hasPages())
        <div class="mt-4">{{ $messages->links() }}</div>
    @endif
@endif
@endsection
