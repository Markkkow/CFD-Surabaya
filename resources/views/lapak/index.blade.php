@extends('layouts.app')

@section('title', 'Pilih Lapak - CFD Surabaya')

@push('styles')
<style>

    .zone-card {
        background: #fff;
        border-radius: 1.25rem;
        border: 1px solid #edf1ef;
        border-left: 6px solid var(--zone-color, var(--cfd-green));
        box-shadow: 0 6px 18px rgba(30,42,38,.05);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .zone-head {
        display: flex;
        align-items: center;
        gap: .9rem;
        padding: 1.1rem 1.4rem;
        background: var(--zone-bg, #f6f8f7);
    }
    .zone-icon {
        width: 46px; height: 46px; flex: none;
        border-radius: .9rem;
        background: #fff;
        color: var(--zone-color, var(--cfd-green));
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
        box-shadow: 0 3px 8px rgba(0,0,0,.06);
    }
    .zone-meta small { color: #6c7a75; }
    .zone-count {
        margin-left: auto;
        font-size: .8rem;
        font-weight: 700;
        color: var(--zone-color, var(--cfd-green));
        background: #fff;
        border: 1px solid var(--zone-color, var(--cfd-green));
        padding: .25rem .7rem;
        border-radius: 999px;
        white-space: nowrap;
    }
    .zone-body { padding: 1.3rem 1.4rem 1.5rem; }

    .street-track { position: relative; }
    .road-line {
        position: absolute;
        top: 0; bottom: 0; left: 50%;
        width: 26px;
        transform: translateX(-50%);
        background-color: #4a5560;
        background-image: repeating-linear-gradient(180deg, #ffd23f 0 12px, transparent 12px 26px);
        background-position: center top;
        background-repeat: repeat-y;
        background-size: 4px 26px;
        border-radius: 8px;
        z-index: 0;
    }
    .street-row {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        margin-bottom: .6rem;
    }
    .street-row:last-child { margin-bottom: 0; }
    .seat-side { flex: 1 1 0; display: flex; flex-wrap: wrap; gap: .5rem; min-width: 0; }
    .seat-side.kiri { justify-content: flex-end; }
    .seat-side.kanan { justify-content: flex-start; }
    .road-gap {
        flex: none;
        width: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 2;
    }
    .road-row-label {
        width: 24px; height: 24px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #4a5560;
        color: #4a5560;
        font-weight: 800;
        font-size: .7rem;
        display: flex; align-items: center; justify-content: center;
        flex: none;
    }

    .seat-input { position: absolute; opacity: 0; width: 0; height: 0; }
    .seat {
        position: relative;
        width: 44px; height: 40px;
        display: flex; align-items: center; justify-content: center;
        font-size: .72rem; font-weight: 700;
        border-radius: 9px 9px 4px 4px;
        border: 2px solid var(--zone-color, var(--cfd-green));
        color: var(--zone-color, var(--cfd-green));
        background: #fff;
        cursor: pointer;
        user-select: none;
        transition: all .12s ease-in-out;
        box-shadow: inset 0 -3px 0 rgba(0,0,0,.05);
    }
    .seat:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,0,0,.12); }

    .seat-input:checked + .seat {
        background: var(--zone-color, var(--cfd-green));
        color: #fff;
        box-shadow: 0 0 0 3px rgba(0,0,0,.05), 0 6px 14px rgba(0,0,0,.18);
        transform: translateY(-2px);
    }
    .seat-input:disabled + .seat,
    .seat.seat-booked {
        background: repeating-linear-gradient(45deg, #eceff1, #eceff1 5px, #e3e7e9 5px, #e3e7e9 10px);
        border-color: #cfd6d3;
        color: #9aa5a1;
        cursor: not-allowed;
        box-shadow: none;
    }
    .seat.seat-booked:hover { transform: none; box-shadow: none; }

    /* ===== Legenda ===== */
    .legend-item { display: flex; align-items: center; gap: .5rem; font-size: .85rem; color: #55625d; }
    .legend-box { width: 24px; height: 22px; border-radius: 6px 6px 3px 3px; border: 2px solid var(--cfd-green); flex: none; }
    .legend-box.is-selected { background: var(--cfd-green); border-color: var(--cfd-green); }
    .legend-box.is-booked {
        background: repeating-linear-gradient(45deg, #eceff1, #eceff1 5px, #e3e7e9 5px, #e3e7e9 10px);
        border-color: #cfd6d3;
    }

    .street-bar {
        max-width: 640px;
        margin: 0 auto 2rem;
        text-align: center;
        padding: .6rem 1rem;
        border-radius: 0 0 60% 60% / 0 0 100% 100%;
        background: linear-gradient(180deg, rgba(26,122,76,.16), rgba(26,122,76,0));
        color: var(--cfd-green-dark);
        font-weight: 700;
        font-size: .78rem;
        letter-spacing: 2px;
    }

    .selection-bar {
        position: sticky;
        bottom: 1rem;
        z-index: 10;
        background: #fff;
        border: 1px solid #e5e9e7;
        border-radius: 1rem;
        box-shadow: 0 -6px 24px rgba(0,0,0,.08);
        padding: 1rem 1.3rem;
    }
    #submitLapakBtn:disabled { opacity: .5; cursor: not-allowed; }

    .event-panel { display: none; }
    .event-panel.is-active { display: block; }
</style>
@endpush

@section('content')
<div class="mb-4">
    <h2 class="fw-heading fw-bold mb-1">
        <i class="bi bi-ticket-perforated-fill me-2" style="color:var(--cfd-green);"></i>Pilih Lapak
    </h2>
    <p class="text-muted mb-0">
        Pilih lapak seperti memilih kursi bioskop. Setiap kategori dagangan punya zona sendiri
        supaya penataan CFD lebih rapi dan tertata.
    </p>
</div>

@if($events->isEmpty())
    <div class="alert alert-warning border-0 shadow-sm text-center py-4">
        <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
        Belum ada event CFD yang aktif saat ini. Silakan cek kembali nanti.
    </div>
@else
    <form action="{{ route('lapak.daftar') }}" method="POST" id="lapakForm">
        @csrf
        <input type="hidden" name="id_event" id="id_event_input" value="{{ $events->first()->id_event }}">

        @if($events->count() > 1)
            <div class="card border-0 rounded-4 shadow-soft mb-4">
                <div class="card-body p-4">
                    <label for="event_selector" class="form-label fw-semibold">
                        <i class="bi bi-calendar-event me-1"></i> Pilih Event CFD
                    </label>
                    <select id="event_selector" class="form-select form-select-lg">
                        @foreach($events as $event)
                            <option value="{{ $event->id_event }}">
                                {{ $event->nama_event }} &middot; {{ \Carbon\Carbon::parse($event->tanggal_event)->format('d M Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        @else
            <div class="d-flex align-items-center gap-2 mb-4 p-3 rounded-4" style="background:var(--cfd-green-light);">
                <i class="bi bi-calendar-check fs-4" style="color:var(--cfd-green);"></i>
                <div>
                    <div class="fw-bold">{{ $events->first()->nama_event }}</div>
                    <div class="small text-muted">
                        {{ \Carbon\Carbon::parse($events->first()->tanggal_event)->format('d M Y') }} &bull;
                        {{ $events->first()->waktu_mulai }}&ndash;{{ $events->first()->waktu_selesai }} &bull;
                        {{ $events->first()->lokasi }}
                    </div>
                </div>
            </div>
        @endif

        <!-- Legenda -->
        <div class="d-flex flex-wrap gap-4 mb-4">
            <div class="legend-item"><span class="legend-box"></span> Tersedia</div>
            <div class="legend-item"><span class="legend-box is-selected"></span> Dipilih</div>
            <div class="legend-item"><span class="legend-box is-booked"></span> Sudah Dipesan</div>
        </div>

        @php
            $zoneMeta = [
                'Kuliner' => ['icon' => 'bi-cup-hot-fill', 'color' => '#e08e0b', 'bg' => '#fff6e9', 'label' => 'Kuliner', 'desc' => 'Makanan & Minuman'],
                'Fashion' => ['icon' => 'bi-bag-heart-fill', 'color' => '#c23b8f', 'bg' => '#fdeef7', 'label' => 'Fashion', 'desc' => 'Pakaian & Aksesoris'],
                'Kerajinan' => ['icon' => 'bi-palette2', 'color' => '#7a4fd1', 'bg' => '#f2eefc', 'label' => 'Kerajinan', 'desc' => 'Kerajinan & Souvenir'],
                'Jasa & Lainnya' => ['icon' => 'bi-tools', 'color' => '#2b7fb8', 'bg' => '#eaf4fb', 'label' => 'Jasa & Lainnya', 'desc' => 'Jasa & kebutuhan lain'],
            ];
        @endphp

        @foreach($events as $event)
            @php $sudahDaftar = $registrasiSaya->get($event->id_event); @endphp
            <div class="event-panel {{ $loop->first ? 'is-active' : '' }}"
                 data-event-panel="{{ $event->id_event }}"
                 data-registered="{{ $sudahDaftar ? '1' : '0' }}">

                @if($sudahDaftar)
                    <div class="alert border-0 shadow-sm d-flex align-items-center gap-3 py-4" style="background:var(--cfd-green-light);">
                        <i class="bi bi-patch-check-fill fs-1" style="color:var(--cfd-green);"></i>
                        <div>
                            <div class="fw-bold fs-5 mb-1">Anda sudah terdaftar di event ini</div>
                            <div class="text-muted mb-1">
                                Lapak <strong>{{ $sudahDaftar->lapak->nomor_lapak ?? '-' }}</strong>
                                (Zona {{ $sudahDaftar->lapak->kategori_lapak ?? '-' }}), status:
                                @if($sudahDaftar->status_pendaftaran === 'Menunggu Verifikasi')
                                    <span class="badge text-bg-warning">Menunggu Verifikasi</span>
                                @elseif($sudahDaftar->status_pendaftaran === 'Terverifikasi')
                                    <span class="badge" style="background:var(--cfd-green);">Terverifikasi</span>
                                @else
                                    <span class="badge text-bg-secondary">{{ $sudahDaftar->status_pendaftaran }}</span>
                                @endif
                            </div>
                            <div class="small text-muted">
                                Satu pedagang hanya boleh memiliki 1 lapak untuk 1 event yang sama,
                                jadi Anda tidak bisa memilih lapak lain di sini selama masih terdaftar.
                            </div>
                        </div>
                    </div>
                @else
                    <div class="street-bar">
                        <i class="bi bi-signpost-2 me-1"></i> ARAH JALAN &middot; {{ strtoupper($event->lokasi) }}
                    </div>

                    @if(empty($lapakByEvent[$event->id_event]))
                        <div class="alert alert-warning border-0 shadow-sm text-center py-4">
                            Belum ada lapak yang diatur untuk event ini.
                        </div>
                    @else
                        @foreach($zoneMeta as $kategori => $meta)
                            @continue(empty($lapakByEvent[$event->id_event][$kategori]))
                            @php
                                $barisList = $lapakByEvent[$event->id_event][$kategori];
                                $totalSeat = collect($barisList)->flatten();
                                $tersedia = $totalSeat->where('status_lapak', 'Tersedia')->count();
                                $contohLokasi = $totalSeat->first()->lokasi_lapak;
                                $contohUkuran = $totalSeat->first()->ukuran_lapak;
                            @endphp
                            <div class="zone-card" style="--zone-color: {{ $meta['color'] }}; --zone-bg: {{ $meta['bg'] }};">
                                <div class="zone-head">
                                    <div class="zone-icon"><i class="bi {{ $meta['icon'] }}"></i></div>
                                    <div class="zone-meta">
                                        <div class="fw-bold">{{ $meta['label'] }} <span class="fw-normal text-muted">&middot; {{ $meta['desc'] }}</span></div>
                                        <small><i class="bi bi-geo-alt"></i> {{ $contohLokasi }} &bull; ukuran {{ $contohUkuran }}</small>
                                    </div>
                                    <span class="zone-count">{{ $tersedia }} / {{ $totalSeat->count() }} tersedia</span>
                                </div>
                                <div class="zone-body">
                                    <div class="street-track">
                                        <div class="road-line"></div>
                                        @foreach($barisList as $baris => $seats)
                                            @php $pasangan = array_chunk($seats, 2); @endphp
                                            @foreach($pasangan as $pair)
                                                @php $kiriSeat = $pair[0] ?? null; $kananSeat = $pair[1] ?? null; @endphp
                                                <div class="street-row">
                                                    <div class="seat-side kiri">
                                                        @if($kiriSeat)
                                                            @php $isBooked = $kiriSeat->status_lapak !== 'Tersedia'; @endphp
                                                            <input type="radio"
                                                                   name="id_lapak"
                                                                   id="lapak-{{ $kiriSeat->id_lapak }}"
                                                                   value="{{ $kiriSeat->id_lapak }}"
                                                                   class="seat-input"
                                                                   data-event="{{ $event->id_event }}"
                                                                   data-nomor="{{ $kiriSeat->nomor_lapak }}"
                                                                   data-zona="{{ $meta['label'] }}"
                                                                   {{ $isBooked ? 'disabled' : '' }}>
                                                            <label for="lapak-{{ $kiriSeat->id_lapak }}"
                                                                   class="seat {{ $isBooked ? 'seat-booked' : '' }}"
                                                                   title="Lapak {{ $kiriSeat->nomor_lapak }}: {{ $isBooked ? 'Sudah dipesan' : 'Tersedia' }}">
                                                                {{ $kiriSeat->nomor_lapak }}
                                                            </label>
                                                        @endif
                                                    </div>
                                                    <div class="road-gap"><span class="road-row-label">{{ $baris }}</span></div>
                                                    <div class="seat-side kanan">
                                                        @if($kananSeat)
                                                            @php $isBooked = $kananSeat->status_lapak !== 'Tersedia'; @endphp
                                                            <input type="radio"
                                                                   name="id_lapak"
                                                                   id="lapak-{{ $kananSeat->id_lapak }}"
                                                                   value="{{ $kananSeat->id_lapak }}"
                                                                   class="seat-input"
                                                                   data-event="{{ $event->id_event }}"
                                                                   data-nomor="{{ $kananSeat->nomor_lapak }}"
                                                                   data-zona="{{ $meta['label'] }}"
                                                                   {{ $isBooked ? 'disabled' : '' }}>
                                                            <label for="lapak-{{ $kananSeat->id_lapak }}"
                                                                   class="seat {{ $isBooked ? 'seat-booked' : '' }}"
                                                                   title="Lapak {{ $kananSeat->nomor_lapak }}: {{ $isBooked ? 'Sudah dipesan' : 'Tersedia' }}">
                                                                {{ $kananSeat->nomor_lapak }}
                                                            </label>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                @endif
            </div>
        @endforeach

        <div class="selection-bar d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" id="selectionBar">
            <div>
                <div class="small text-uppercase text-muted fw-bold mb-1">Lapak Dipilih</div>
                <div id="selectionSummary" class="fw-bold fs-5">
                    <span class="text-muted fw-normal fs-6"><i class="bi bi-cursor"></i> Silakan klik salah satu lapak di atas</span>
                </div>
            </div>
            <button type="submit" id="submitLapakBtn" class="btn btn-success btn-lg px-5 fw-bold rounded-3" disabled>
                <i class="bi bi-check2-circle me-1"></i> Konfirmasi & Daftar Tenant
            </button>
        </div>
    </form>
@endif
@endsection

@push('scripts')
<script>
(function () {
    const form = document.getElementById('lapakForm');
    if (!form) return;

    const eventSelector = document.getElementById('event_selector');
    const eventInput = document.getElementById('id_event_input');
    const panels = document.querySelectorAll('.event-panel');
    const submitBtn = document.getElementById('submitLapakBtn');
    const summary = document.getElementById('selectionSummary');
    const selectionBar = document.getElementById('selectionBar');

    function activatePanel(eventId) {
        let isRegistered = false;

        panels.forEach(function (panel) {
            const match = panel.dataset.eventPanel === String(eventId);
            panel.classList.toggle('is-active', match);
            if (match) isRegistered = panel.dataset.registered === '1';
        });

        // Nonaktifkan seat di luar event yang aktif supaya tidak ikut ter-submit,
        // aktifkan kembali seat (yang memang tersedia) di event yang dipilih.
        document.querySelectorAll('.seat-input').forEach(function (input) {
            const inActivePanel = input.dataset.event === String(eventId);
            if (!inActivePanel) {
                input.checked = false;
            }
            input.disabled = !inActivePanel || input.dataset.booked === '1';
        });

        eventInput.value = eventId;
        selectionBar.style.display = isRegistered ? 'none' : '';
        resetSelection();
    }

    function resetSelection() {
        submitBtn.disabled = true;
        summary.innerHTML = '<span class="text-muted fw-normal fs-6"><i class="bi bi-cursor"></i> Silakan klik salah satu lapak di atas</span>';
    }

    // Tandai state "booked" asli sebelum kita mulai mengubah disabled secara dinamis.
    document.querySelectorAll('.seat-input').forEach(function (input) {
        if (input.disabled) input.dataset.booked = '1';
    });

    if (eventSelector) {
        eventSelector.addEventListener('change', function () {
            activatePanel(this.value);
        });
    }
    // Inisialisasi tampilan sesuai event yang aktif saat halaman dimuat
    // (berlaku juga saat cuma ada 1 event, tanpa dropdown pemilih).
    activatePanel(eventSelector ? eventSelector.value : eventInput.value);

    document.querySelectorAll('.seat-input').forEach(function (input) {
        input.addEventListener('change', function () {
            if (!this.checked) return;
            submitBtn.disabled = false;
            summary.innerHTML = '<i class="bi bi-geo-alt-fill me-1" style="color:var(--cfd-green);"></i>' +
                'Zona ' + this.dataset.zona + ', Nomor <span style="color:var(--cfd-green);">' + this.dataset.nomor + '</span>';
        });
    });
})();
</script>
@endpush
