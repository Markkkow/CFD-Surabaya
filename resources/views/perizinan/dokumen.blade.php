@extends('layouts.app')

@section('title', 'Surat Izin Berjualan - CFD Surabaya')

@push('styles')
<style>
    .izin-page { max-width: 900px; margin: 0 auto; }
    .izin-paper { background:#fff; border:1px solid #dfe6e2; box-shadow:0 .5rem 2rem rgba(30,42,38,.08); padding:48px 56px; }
    .izin-header { text-align:center; border-bottom:2px solid #1f5f45; padding-bottom:18px; margin-bottom:28px; }
    .izin-header .small-title { font-size:.9rem; font-weight:700; letter-spacing:.08em; }
    .izin-header h1 { font-size:1.45rem; font-weight:800; margin:.35rem 0 .15rem; }
    .izin-header p { margin:0; color:#5f6c66; }
    .izin-number { text-align:center; font-weight:700; margin-bottom:28px; }
    .izin-body { line-height:1.8; }
    .data-table { width:100%; border-collapse:collapse; margin:16px 0 22px; font-size:.88rem; }
    .data-table td { border:1px solid #dfe6e2; padding:5px 9px; line-height:1.35; vertical-align:middle; }
    .data-table td:first-child { width:30%; font-weight:700; background:#f7faf8; }
    .data-table td:nth-child(2) { width:70%; }
    .izin-note { background:#f7faf8; border-left:4px solid #1f5f45; padding:14px 16px; margin:22px 0; }
    .signature { margin-top:34px; margin-left:auto; width:250px; text-align:center; font-size:.9rem; }
    .signature-space { height:58px; display:flex; align-items:flex-end; justify-content:center; }
    .signature-svg { width:185px; height:62px; overflow:visible; }
    .signature-svg path { fill:none; stroke:#1f2937; stroke-width:2.6; stroke-linecap:round; stroke-linejoin:round; }
    .signature-label { margin-top:2px; font-size:.76rem; color:#6b756f; }
    .print-actions { max-width:900px; margin:0 auto 18px; display:flex; gap:8px; justify-content:flex-end; }
    @media (max-width: 767px) {
        .izin-paper { padding:28px 20px; }
        .print-actions { justify-content:stretch; }
        .print-actions .btn { flex:1; }
    }
    @media print {
        @page { size:A4; margin:16mm; }
        body { background:#fff !important; }
        nav, footer, .print-actions, .alert { display:none !important; }
        .izin-page { max-width:none; }
        .izin-paper { box-shadow:none; border:0; padding:0; }
    }
</style>
@endpush

@section('content')
<div class="izin-page py-3">
    <div class="print-actions">
        <button type="button" class="btn btn-success fw-bold" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Cetak / Simpan PDF
        </button>
        <button type="button" class="btn btn-outline-secondary" onclick="window.close()">
            Tutup
        </button>
    </div>

    <div class="izin-paper">
        <div class="izin-header">
            <div class="small-title">PEMERINTAH / PENGELOLA CAR FREE DAY SURABAYA</div>
            <h1>SURAT IZIN BERJUALAN</h1>
            <p>CAR FREE DAY (CFD) SURABAYA</p>
        </div>

        <div class="izin-number">
            Nomor: CFD-SBY/{{ $perizinan->event->tanggal_event?->format('Y') ?? now()->format('Y') }}/{{ str_pad($perizinan->id_perizinan, 4, '0', STR_PAD_LEFT) }}
        </div>

        <div class="izin-body">
            <p>Dengan ini menerangkan bahwa pedagang berikut telah memperoleh persetujuan untuk berjualan pada kegiatan Car Free Day (CFD) Surabaya sesuai dengan data pendaftaran yang telah diverifikasi.</p>

            <table class="data-table">
                <tr><td>NIK Pedagang</td><td>{{ $perizinan->pedagang->nik_pedagang }}</td></tr>
                <tr><td>Nama Pedagang</td><td>{{ $perizinan->pedagang->nama_pedagang }}</td></tr>
                <tr><td>Nama Usaha</td><td>{{ $perizinan->pedagang->nama_usaha }}</td></tr>
                <tr><td>No. Telepon</td><td>{{ $perizinan->pedagang->no_telepon }}</td></tr>
                <tr><td>Event CFD</td><td>{{ $perizinan->event->nama_event }}</td></tr>
                <tr><td>Tanggal Event</td><td>{{ optional($perizinan->event->tanggal_event)->format('d F Y') }}</td></tr>
                <tr><td>Waktu</td><td>{{ substr($perizinan->event->waktu_mulai,0,5) }} - {{ substr($perizinan->event->waktu_selesai,0,5) }}</td></tr>
                <tr><td>Lokasi</td><td>{{ $perizinan->event->lokasi }}</td></tr>
                <tr><td>Nomor Lapak</td><td>{{ $pendaftaran->lapak->nomor_lapak ?? '-' }}</td></tr>
                <tr><td>Zona</td><td>{{ $pendaftaran->lapak->kategori_lapak ?? '-' }}</td></tr>
                <tr><td>Status Perizinan</td><td><strong>{{ $perizinan->status_perizinan }}</strong></td></tr>
                <tr><td>Tanggal Berlaku</td><td>{{ optional($perizinan->tanggal_berlaku)->format('d F Y') }}</td></tr>
            </table>

            <div class="izin-note">
                Dokumen ini merupakan hasil pencatatan persetujuan pendaftaran tenant. Izin berlaku sesuai event CFD dan lapak yang tercantum pada dokumen ini.
            </div>

            <p>Demikian surat izin ini dibuat sebagai dokumen pencatatan bahwa pedagang yang bersangkutan telah memperoleh persetujuan untuk berjualan pada event tersebut.</p>
        </div>

        <div class="signature">
            <div>Surabaya, {{ optional($perizinan->tanggal_pengajuan)->format('d F Y') }}</div>
            <div>Admin / Pengelola CFD Surabaya</div>
            <div class="signature-space" aria-label="Template tanda tangan">
                <svg class="signature-svg" viewBox="0 0 220 70" role="img" aria-label="Tanda tangan template">
                    <path d="M8 52 C20 40, 18 26, 28 25 C37 24, 25 48, 37 48 C49 48, 50 17, 58 19 C67 21, 51 52, 68 50 C84 48, 82 27, 91 28 C101 30, 86 51, 105 49 C120 47, 120 34, 129 35 C137 36, 127 51, 143 49 C158 47, 157 29, 166 31 C174 33, 164 50, 180 47 C190 45, 197 37, 210 40"/>
                    <path d="M42 58 C80 61, 125 58, 176 58 C190 58, 202 56, 212 53"/>
                </svg>
            </div>
            <div class="signature-label">Template tanda tangan pengelola</div>
            <strong>Admin / Pengelola CFD Surabaya</strong>
        </div>
    </div>
</div>
@endsection
