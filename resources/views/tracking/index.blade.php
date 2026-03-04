@extends('layouts.app')

@section('content')
<style>
    /* ─── ESCAPE DEL PADDING DEL MAIN ───────────────────────── */
    .trk-escape {
        margin: -1rem -1rem -1rem -1rem;
        /* El footer es fixed de ~45px + el main tiene mb-16 (64px) + mt-6 (24px) + header (~64px)
           Usamos 100dvh para que en mobile también funcione correctamente */
        height: calc(100dvh - 160px);
        min-height: 420px;
        display: flex;
        flex-direction: column;
        border-radius: inherit;
        overflow: hidden;
    }

    /* ─── TOKENS AESCALA ─────────────────────────────────────── */
    :root {
        --trk-sidebar-bg:  #2d3748;
        --trk-sidebar-bg2: #374151;
        --trk-sidebar-bdr: #4a5568;
        --trk-orange:      #e8490f;
        --trk-orange-lt:   rgba(232,73,15,.12);
        --trk-orange-bdr:  rgba(232,73,15,.35);
        --trk-green:       #28a745;
        --trk-green-lt:    rgba(40,167,69,.12);
        --trk-green-bdr:   rgba(40,167,69,.35);
        --trk-yellow:      #ffc107;
        --trk-red:         #dc3545;
        --trk-purple:      #6f42c1;
        --trk-white:       #ffffff;
        --trk-text:        #e2e8f0;
        --trk-muted:       #a0aec0;
        --trk-radius:      7px;
        --trk-shadow:      0 4px 20px rgba(0,0,0,.35);
        --trk-font-h:      'Figtree', sans-serif;
        --trk-font-m:      'Figtree', sans-serif;
    }

    /* ─── TOPBAR INTERNA ─────────────────────────────────────── */
    .trk-topbar {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0 16px;
        height: 46px;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        font-family: var(--trk-font-h);
    }
    .trk-topbar-title {
        font-weight: 700;
        font-size: 14px;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .trk-topbar-title svg { color: var(--trk-orange); }
    .trk-topbar-sep { flex: 1; }
    .trk-last-upd { font-size: 10px; color: #6b7280; display: none; }
    .trk-live-badge {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 10px; font-weight: 600; color: var(--trk-green);
        background: var(--trk-green-lt); border: 1px solid var(--trk-green-bdr);
        border-radius: 20px; padding: 3px 9px; white-space: nowrap;
    }
    .trk-live-dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: var(--trk-green);
        animation: trkPulse 1.5s ease-in-out infinite;
    }

    /* ─── BODY ───────────────────────────────────────────────── */
    .trk-body {
        flex: 1;
        display: flex;
        min-height: 0;
        position: relative;
    }

    /* ─── SIDEBAR ────────────────────────────────────────────── */
    .trk-sidebar {
        width: 290px;
        min-width: 260px;
        flex-shrink: 0;
        background: var(--trk-sidebar-bg);
        border-right: 1px solid var(--trk-sidebar-bdr);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        color: var(--trk-text);
        font-family: var(--trk-font-m);
        font-size: 13px;
        transition: transform .28s ease;
    }

    /* ─── TABS ───────────────────────────────────────────────── */
    .trk-tabs { display: flex; border-bottom: 1px solid var(--trk-sidebar-bdr); flex-shrink: 0; }
    .trk-tab {
        flex: 1; padding: 11px 0; text-align: center; cursor: pointer;
        font-weight: 600; font-size: 11px; color: var(--trk-muted);
        letter-spacing: .4px; transition: all .2s;
        border-bottom: 2px solid transparent;
    }
    .trk-tab:hover { color: var(--trk-text); }
    .trk-tab.active { color: var(--trk-orange); border-bottom-color: var(--trk-orange); }

    /* ─── PANELS ─────────────────────────────────────────────── */
    .trk-panel { display: none; flex-direction: column; flex: 1; overflow: hidden; }
    .trk-panel.active { display: flex; }

    /* ─── SEARCH INPUT ───────────────────────────────────────── */
    .trk-search { padding: 10px; flex-shrink: 0; }
    .trk-search input {
        width: 100%;
        background: var(--trk-sidebar-bg2);
        border: 1px solid var(--trk-sidebar-bdr);
        border-radius: var(--trk-radius);
        color: var(--trk-text);
        font-family: var(--trk-font-m);
        font-size: 12px;
        padding: 7px 11px;
        outline: none;
        transition: border-color .2s;
    }
    .trk-search input:focus { border-color: var(--trk-orange); }
    .trk-search input::placeholder { color: var(--trk-muted); }

    /* ─── DEVICE LIST ────────────────────────────────────────── */
    .trk-device-list { flex: 1; overflow-y: auto; padding: 0 8px 8px; }
    .trk-device-list::-webkit-scrollbar { width: 3px; }
    .trk-device-list::-webkit-scrollbar-track { background: transparent; }
    .trk-device-list::-webkit-scrollbar-thumb { background: var(--trk-sidebar-bdr); border-radius: 2px; }

    .trk-card {
        background: var(--trk-sidebar-bg2);
        border: 1px solid var(--trk-sidebar-bdr);
        border-radius: var(--trk-radius);
        padding: 9px 11px;
        margin-bottom: 5px;
        cursor: pointer;
        transition: all .15s;
    }
    .trk-card:hover   { border-color: var(--trk-orange); }
    .trk-card.selected {
        border-color: var(--trk-orange);
        background: rgba(232,73,15,.1);
        border-left: 3px solid var(--trk-orange);
    }
    .trk-card-name   { font-weight: 700; font-size: 12px; color: var(--trk-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .trk-card-serial { font-size: 10px; color: var(--trk-muted); margin-top: 1px; }
    .trk-card-user   { font-size: 10px; color: #f6ad6e; margin-top: 2px; }
    .trk-card-meta   { display: flex; gap: 5px; margin-top: 5px; flex-wrap: wrap; }

    /* ─── ASSIGN USER SELECT ─────────────────────────────────── */
    .trk-assign-wrap {
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid var(--trk-sidebar-bdr);
        /* Evita que el click en el select propague al card */
    }
    .trk-assign-label {
        font-size: 9px;
        color: var(--trk-muted);
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-bottom: 4px;
        display: block;
    }
    .trk-assign-row {
        display: flex;
        gap: 5px;
        align-items: center;
    }
    .trk-assign-select {
        flex: 1;
        background: var(--trk-sidebar-bg);
        border: 1px solid var(--trk-sidebar-bdr);
        border-radius: var(--trk-radius);
        color: var(--trk-text);
        font-family: var(--trk-font-m);
        font-size: 11px;
        padding: 5px 8px;
        outline: none;
        cursor: pointer;
        transition: border-color .2s;
        color-scheme: dark;
        min-width: 0;
    }
    .trk-assign-select:focus { border-color: var(--trk-orange); }
    .trk-assign-select option { background: var(--trk-sidebar-bg2); }
    .trk-assign-btn {
        flex-shrink: 0;
        background: var(--trk-orange);
        border: none;
        color: #fff;
        border-radius: var(--trk-radius);
        padding: 5px 9px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        transition: background .2s;
        white-space: nowrap;
    }
    .trk-assign-btn:hover { background: #c93d0b; }
    .trk-assign-btn:disabled { background: var(--trk-sidebar-bdr); cursor: not-allowed; }
    .trk-assign-feedback {
        font-size: 10px;
        margin-top: 4px;
        display: none;
    }
    .trk-assign-feedback.ok  { color: #4ade80; display: block; }
    .trk-assign-feedback.err { color: #f87171; display: block; }

    /* ─── BADGES ─────────────────────────────────────────────── */
    .trk-badge { display: inline-flex; align-items: center; gap: 3px; font-size: 10px; padding: 2px 6px; border-radius: 20px; font-weight: 600; }
    .trk-badge-green  { background: var(--trk-green-lt);        color: #4ade80; border: 1px solid var(--trk-green-bdr); }
    .trk-badge-yellow { background: rgba(255,193,7,.12);         color: var(--trk-yellow); border: 1px solid rgba(255,193,7,.3); }
    .trk-badge-red    { background: rgba(220,53,69,.12);         color: #f87171; border: 1px solid rgba(220,53,69,.3); }
    .trk-badge-orange { background: var(--trk-orange-lt);        color: #fb923c; border: 1px solid var(--trk-orange-bdr); }
    .trk-badge-muted  { background: rgba(160,174,192,.1);        color: var(--trk-muted); border: 1px solid rgba(160,174,192,.2); }

    /* ─── HISTORY FORM ───────────────────────────────────────── */
    .trk-hist-form { padding: 10px; display: flex; flex-direction: column; gap: 9px; overflow-y: auto; flex: 1; }
    .trk-hist-form::-webkit-scrollbar { width: 3px; }
    .trk-hist-form::-webkit-scrollbar-thumb { background: var(--trk-sidebar-bdr); }

    .trk-label { font-size: 10px; color: var(--trk-muted); text-transform: uppercase; letter-spacing: .5px; display: block; margin-bottom: 3px; font-weight: 600; }
    .trk-hist-form select,
    .trk-hist-form input[type="date"] {
        width: 100%;
        background: var(--trk-sidebar-bg2);
        border: 1px solid var(--trk-sidebar-bdr);
        border-radius: var(--trk-radius);
        color: var(--trk-text);
        font-family: var(--trk-font-m);
        font-size: 12px;
        padding: 7px 11px;
        outline: none;
        transition: border-color .2s;
        color-scheme: dark;
    }
    .trk-hist-form select:focus,
    .trk-hist-form input[type="date"]:focus { border-color: var(--trk-orange); }
    .trk-hist-form select option { background: var(--trk-sidebar-bg2); }
    .trk-section-sep { border: none; border-top: 1px solid var(--trk-sidebar-bdr); margin: 2px 0; }

    .trk-btn { padding: 8px 14px; border-radius: var(--trk-radius); border: none; font-family: var(--trk-font-h); font-weight: 700; font-size: 11px; cursor: pointer; transition: all .2s; letter-spacing: .3px; white-space: nowrap; }
    .trk-btn-primary { background: var(--trk-orange); color: #fff; }
    .trk-btn-primary:hover { background: #c93d0b; }
    .trk-btn-ghost { background: transparent; color: var(--trk-muted); border: 1px solid var(--trk-sidebar-bdr); }
    .trk-btn-ghost:hover { border-color: var(--trk-text); color: var(--trk-text); }
    .trk-btn-icon { padding: 7px 10px; font-size: 12px; line-height: 1; }

    .trk-hist-info { font-size: 11px; color: var(--trk-muted); padding: 2px; line-height: 1.5; }
    .trk-hist-info strong { color: var(--trk-text); }

    .trk-stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
    .trk-stat-box { background: var(--trk-sidebar-bg2); border: 1px solid var(--trk-sidebar-bdr); border-radius: var(--trk-radius); padding: 8px; text-align: center; }
    .trk-stat-val { font-size: 20px; font-weight: 800; color: var(--trk-orange); line-height: 1; }
    .trk-stat-val.green { color: #4ade80; }
    .trk-stat-lbl { font-size: 9px; color: var(--trk-muted); margin-top: 3px; text-transform: uppercase; letter-spacing: .4px; }

    /* ─── MAP ────────────────────────────────────────────────── */
    .trk-map-wrap { flex: 1; position: relative; min-width: 0; }
    #trk-map { width: 100%; height: 100%; }

    .trk-map-controls {
        position: absolute;
        top: 10px;
        left: 50px;   /* a la derecha del zoom control */
        z-index: 500;
        display: flex;
        gap: 4px;
    }
    .trk-style-btn {
        background: #fff; border: 1px solid #ccc; color: #374151;
        border-radius: 5px; padding: 5px 10px;
        font-family: var(--trk-font-h); font-size: 10px; font-weight: 600;
        cursor: pointer; transition: all .18s;
        box-shadow: 0 1px 4px rgba(0,0,0,.2); white-space: nowrap;
    }
    .trk-style-btn.active { background: var(--trk-orange); color: #fff; border-color: var(--trk-orange); }
    .trk-style-btn:hover:not(.active) { background: #f3f4f6; border-color: #999; }

    /* Info panel */
    .trk-info-panel {
        position: absolute; top: 10px; right: 10px; z-index: 500;
        background: var(--trk-sidebar-bg); border: 1px solid var(--trk-sidebar-bdr);
        border-radius: 9px; padding: 13px; width: 215px;
        box-shadow: var(--trk-shadow); display: none;
        animation: trkFadeIn .2s ease;
        color: var(--trk-text); font-family: var(--trk-font-m);
    }
    .trk-info-panel.visible { display: block; }
    .trk-ip-title { font-weight: 700; font-size: 13px; color: var(--trk-orange); margin-bottom: 4px; }
    .trk-ip-user  { font-size: 10px; color: #f6ad6e; margin-bottom: 8px; display: none; }
    .trk-info-row { display: flex; justify-content: space-between; align-items: center; padding: 3px 0; border-bottom: 1px solid var(--trk-sidebar-bdr); }
    .trk-info-row:last-child { border-bottom: none; }
    .trk-ik { color: var(--trk-muted); font-size: 10px; }
    .trk-iv { font-size: 11px; color: var(--trk-text); font-weight: 600; }
    .trk-ip-close-btn { margin-top: 10px; width: 100%; background: transparent; border: 1px solid var(--trk-sidebar-bdr); color: var(--trk-muted); border-radius: 5px; padding: 4px; cursor: pointer; font-size: 10px; font-family: var(--trk-font-m); transition: all .15s; }
    .trk-ip-close-btn:hover { border-color: var(--trk-orange); color: var(--trk-orange); }

    /* Route player */
    .trk-route-player {
        position: absolute; bottom: 16px; left: 50%; transform: translateX(-50%);
        z-index: 500; background: var(--trk-sidebar-bg); border: 1px solid var(--trk-sidebar-bdr);
        border-radius: 9px; padding: 11px 14px;
        min-width: 280px; max-width: calc(100% - 40px);
        box-shadow: var(--trk-shadow); display: none;
        color: var(--trk-text); font-family: var(--trk-font-m);
    }
    .trk-route-player.visible { display: block; }
    .trk-rp-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 7px; }
    .trk-rp-title  { font-size: 12px; font-weight: 700; color: var(--trk-text); }
    .trk-rp-close  { background: none; border: none; color: var(--trk-muted); cursor: pointer; font-size: 14px; line-height: 1; }
    .trk-rp-close:hover { color: var(--trk-orange); }
    .trk-rp-slider { width: 100%; accent-color: var(--trk-orange); cursor: pointer; }
    .trk-rp-meta   { display: flex; justify-content: space-between; margin-top: 5px; font-size: 10px; color: var(--trk-muted); }

    /* Loading */
    .trk-loading { position: absolute; inset: 0; z-index: 999; background: rgba(45,55,72,.6); backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; }
    .trk-loading.show { display: flex; }
    .trk-spinner { width: 36px; height: 36px; border: 3px solid var(--trk-sidebar-bdr); border-top-color: var(--trk-orange); border-radius: 50%; animation: trkSpin .7s linear infinite; }

    /* ─── LEAFLET OVERRIDES ──────────────────────────────────── */
    .leaflet-control-zoom { border: 1px solid #ccc !important; border-radius: 5px !important; }
    .leaflet-control-zoom a { background: #fff !important; color: #374151 !important; border-color: #ddd !important; font-weight: 700 !important; }
    .leaflet-control-zoom a:hover { background: #f3f4f6 !important; }
    .leaflet-popup-content-wrapper { background: var(--trk-sidebar-bg); color: var(--trk-text); border: 1px solid var(--trk-sidebar-bdr); border-radius: 8px; font-family: var(--trk-font-m); font-size: 12px; }
    .leaflet-popup-tip { background: var(--trk-sidebar-bg); }
    .leaflet-control-attribution { font-size: 9px !important; }

    /* ─── MOBILE TOGGLE ──────────────────────────────────────── */
    .trk-toggle-sidebar {
        display: none;
        position: absolute;
        top: 80px;    /* debajo del zoom +/- */
        left: 9px;
        z-index: 600;
        background: var(--trk-orange);
        border: none;
        color: #fff;
        border-radius: 6px;
        width: 32px;
        height: 32px;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 15px;
        line-height: 1;
        box-shadow: 0 2px 8px rgba(0,0,0,.3);
    }

    /* ─── TOAST ──────────────────────────────────────────────── */
    .trk-toast { position: fixed; bottom: 80px; right: 20px; z-index: 9999; background: var(--trk-sidebar-bg); border: 1px solid var(--trk-sidebar-bdr); border-radius: var(--trk-radius); padding: 9px 14px; font-size: 12px; color: var(--trk-text); box-shadow: var(--trk-shadow); font-family: var(--trk-font-m); display: none; animation: trkFadeIn .2s ease; max-width: 260px; }
    .trk-toast.show    { display: block; }
    .trk-toast.error   { border-color: var(--trk-red);   color: #f87171; }
    .trk-toast.success { border-color: var(--trk-green); color: #4ade80; }

    /* ─── ANIMATIONS ─────────────────────────────────────────── */
    @keyframes trkFadeIn { from{opacity:0;transform:translateY(-5px)}to{opacity:1;transform:translateY(0)} }
    @keyframes trkSpin   { to{transform:rotate(360deg)} }
    @keyframes trkPulse  { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.4)} }

    /* ─── MARKERS ────────────────────────────────────────────── */
    .trk-dm-inner { width: 14px; height: 14px; background: var(--trk-orange); border: 2px solid #fff; border-radius: 50%; box-shadow: 0 0 0 3px rgba(232,73,15,.35); animation: trkPulse 2s infinite; }
    .trk-dm-inner.history { background: var(--trk-purple); box-shadow: 0 0 0 3px rgba(111,66,193,.35); animation: none; }

    /* ─── RESPONSIVE ─────────────────────────────────────────── */
    @media (max-width: 768px) {
        .trk-escape { height: calc(100dvh - 130px); margin: -.75rem -.75rem -.75rem -.75rem; }
        .trk-sidebar { position: absolute; top: 0; bottom: 0; left: 0; z-index: 2000; width: 280px; transform: translateX(-100%); }
        .trk-sidebar.open { transform: translateX(0); }
        .trk-sb-overlay { display: none; position: absolute; inset: 0; z-index: 540; background: rgba(0,0,0,.45); }
        .trk-sidebar.open ~ .trk-sb-overlay { display: block; }
        .trk-toggle-sidebar { display: flex; }
        .trk-route-player { left: 8px; right: 8px; transform: none; min-width: 0; bottom: 64px; }
        .trk-info-panel { width: 195px; top: 10px; right: 8px; }
        .trk-map-controls { left: 50px; }
    }
    @media (max-width: 480px) {
        .trk-escape { height: calc(100dvh - 115px); margin: -.5rem -.5rem -.5rem -.5rem; }
        .trk-sidebar { width: 100%; }
    }
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="trk-escape">

    {{-- Topbar interna --}}
    <div class="trk-topbar">
        <div class="trk-topbar-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Dispositivos en Mapa
        </div>
        <div class="trk-topbar-sep"></div>
        <span class="trk-last-upd" id="trk-last-update"></span>
        <div class="trk-live-badge">
            <span class="trk-live-dot"></span> EN VIVO
        </div>
    </div>

    {{-- Body --}}
    <div class="trk-body">

        {{-- Sidebar --}}
        <aside class="trk-sidebar" id="trk-sidebar">

            <div class="trk-tabs">
                <div class="trk-tab active" data-trk-tab="devices">Dispositivos</div>
                <div class="trk-tab"        data-trk-tab="history">Recorrido</div>
            </div>

            {{-- Panel Dispositivos --}}
            <div class="trk-panel active" id="trk-panel-devices">
                <div class="trk-search">
                    <input type="text" id="trk-search" placeholder="Buscar dispositivo o usuario…" />
                </div>
                <div class="trk-device-list" id="trk-device-list">
                    @forelse($dispositivos as $d)
                        @php
                            $loc      = $d['last_location'];
                            $bat      = $loc?->battery ?? null;
                            $batCls   = $bat === null ? 'muted' : ($bat > 50 ? 'green' : ($bat > 20 ? 'yellow' : 'red'));
                            $usuario  = $d['user'] ?? null;
                            $deviceId = $d['id'];
                        @endphp
                        <div class="trk-card"
                            data-id="{{ $deviceId }}"
                            data-lat="{{ $loc?->lat ?? '' }}"
                            data-lng="{{ $loc?->lng ?? '' }}"
                            data-label="{{ $d['brand'] }} {{ $d['model'] }}"
                            data-serial="{{ $d['device_serial'] }}"
                            data-user="{{ $usuario->nombre_completo ?? '' }}"
                            data-battery="{{ $bat ?? '–' }}"
                            data-updated="{{ $loc?->request_at ?? '–' }}">

                            <div class="trk-card-name">{{ $d['brand'] }} {{ $d['model'] }}</div>
                            <div class="trk-card-serial">{{ $d['device_serial'] }}</div>

                            @if($usuario)
                                <div class="trk-card-user">👤 {{ $usuario->nombre_completo }}</div>
                            @endif

                            <div class="trk-card-meta">
                                @if($bat !== null)
                                    <span class="trk-badge trk-badge-{{ $batCls }}">🔋 {{ $bat }}%</span>
                                @endif
                                @if($loc)
                                    <span class="trk-badge trk-badge-orange">📍 Activo</span>
                                @else
                                    <span class="trk-badge trk-badge-muted">Sin señal</span>
                                @endif
                            </div>

                            {{-- Asignación de usuario ─────────────────── --}}
                            <div class="trk-assign-wrap" onclick="event.stopPropagation()">
                                <span class="trk-assign-label">Asignar usuario</span>
                                <div class="trk-assign-row">
                                    <select class="trk-assign-select" data-device="{{ $deviceId }}">
                                        <option value="">Sin asignar</option>
                                        {{-- Usuario actualmente asignado (si lo tiene) --}}
                                        @if($usuario)
                                            <option value="{{ $usuario->id }}" selected>
                                                {{ $usuario->nombre_completo }}
                                            </option>
                                        @endif
                                        {{-- Usuarios disponibles (sin dispositivo) --}}
                                        @foreach($users as $uid => $uname)
                                            <option value="{{ $uid }}">{{ $uname }}</option>
                                        @endforeach
                                    </select>
                                    <button class="trk-assign-btn"
                                        onclick="assignUser(this, {{ $deviceId }})"
                                        title="Guardar asignación">
                                        ✓
                                    </button>
                                </div>
                                <div class="trk-assign-feedback" id="trk-feedback-{{ $deviceId }}"></div>
                            </div>

                        </div>
                    @empty
                        <div style="padding:24px 12px;text-align:center;color:var(--trk-muted);font-size:12px;line-height:1.7">
                            No hay dispositivos<br>registrados aún
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Panel Recorrido --}}
            <div class="trk-panel" id="trk-panel-history">
                <div class="trk-hist-form">
                    <div>
                        <label class="trk-label">Dispositivo</label>
                        <select id="trk-hist-device">
                            <option value="">— Todos —</option>
                            @foreach($dispositivos as $d)
                                @php $u = $d['user'] ?? null; @endphp
                                <option value="{{ $d['id'] }}">
                                    {{ $d['brand'] }} {{ $d['model'] }}{{ $u ? ' — '.$u->nombre_completo : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <hr class="trk-section-sep" />
                    <div>
                        <label class="trk-label">Día a consultar</label>
                        <input type="date" id="trk-hist-day" value="{{ date('Y-m-d') }}" />
                    </div>
                    <div style="display:flex;gap:6px">
                        <button class="trk-btn trk-btn-primary" id="trk-btn-history" style="flex:1">Ver Recorrido</button>
                        <button class="trk-btn trk-btn-ghost trk-btn-icon" id="trk-btn-clear" title="Limpiar">✕</button>
                    </div>
                    <div id="trk-hist-info" class="trk-hist-info" style="display:none"></div>
                    <div id="trk-hist-stats" style="display:none">
                        <div class="trk-stats-grid">
                            <div class="trk-stat-box">
                                <div class="trk-stat-val" id="trk-stat-pts">0</div>
                                <div class="trk-stat-lbl">Puntos</div>
                            </div>
                            <div class="trk-stat-box">
                                <div class="trk-stat-val green" id="trk-stat-km">0</div>
                                <div class="trk-stat-lbl">Km aprox.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </aside>

        {{-- Overlay mobile --}}
        <div class="trk-sb-overlay" id="trk-sb-overlay"></div>

        {{-- Mapa --}}
        <div class="trk-map-wrap">
            <div id="trk-map"></div>

            {{-- Toggle sidebar mobile --}}
            <button class="trk-toggle-sidebar" id="trk-toggle-sb">☰</button>

            {{-- Controles de estilo del mapa --}}
            <div class="trk-map-controls">
                <button class="trk-style-btn active" data-style="color">🗺️ Color</button>
                <button class="trk-style-btn"        data-style="dark" >🌙 Oscuro</button>
                <button class="trk-style-btn"        data-style="sat"  >🛰️ Satélite</button>
            </div>

            {{-- Info panel --}}
            <div class="trk-info-panel" id="trk-info-panel">
                <div class="trk-ip-title" id="trk-ip-title">—</div>
                <div class="trk-ip-user"  id="trk-ip-user"></div>
                <div id="trk-ip-rows"></div>
                <button class="trk-ip-close-btn" onclick="document.getElementById('trk-info-panel').classList.remove('visible')">Cerrar</button>
            </div>

            {{-- Route player --}}
            <div class="trk-route-player" id="trk-route-player">
                <div class="trk-rp-header">
                    <div class="trk-rp-title">▶ Reproducir recorrido</div>
                    <button class="trk-rp-close" id="trk-rp-close">✕</button>
                </div>
                <input type="range" class="trk-rp-slider" id="trk-rp-slider" min="0" value="0" />
                <div class="trk-rp-meta">
                    <span id="trk-rp-current">—</span>
                    <span id="trk-rp-total">0 puntos</span>
                </div>
            </div>

            {{-- Loading --}}
            <div class="trk-loading" id="trk-loading">
                <div class="trk-spinner"></div>
            </div>
        </div>{{-- /map-wrap --}}

    </div>{{-- /body --}}
</div>{{-- /escape --}}

<div class="trk-toast" id="trk-toast"></div>

@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ── TILE LAYERS ───────────────────────────────────────────────
const tileLayers = {
    color: L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap © CARTO', maxZoom: 19, subdomains: 'abcd'
    }),
    dark: L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap © CARTO', maxZoom: 19, subdomains: 'abcd'
    }),
    sat: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '© Esri', maxZoom: 19
    })
};
let currentStyle = 'color';

// ── MAP INIT ──────────────────────────────────────────────────
const map = L.map('trk-map', {
    center: [4.6097, -74.0817],
    zoom: 12,
    zoomControl: true,
    preferCanvas: true
});
tileLayers.color.addTo(map);

// ── STATE ─────────────────────────────────────────────────────
let realtimeMarkers = {};
let historyLayer    = null;
let historyPoints   = [];
let selectedDevice  = null;

// ── ICONS ─────────────────────────────────────────────────────
function makeIcon(type = 'live') {
    return L.divIcon({
        className: '',
        html: `<div style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
                 <div class="trk-dm-inner${type==='history'?' history':''}"></div>
               </div>`,
        iconSize: [32,32], iconAnchor: [16,16]
    });
}
const startIcon = L.divIcon({
    className: '',
    html: `<div style="margin-left:35px;width:40px;text-align:center;background:#28a745;color:#fff;font-size:9px;font-weight:700;font-family:'Figtree',sans-serif;padding:2px 2px 2px 2px;border-radius:5px;white-space:nowrap;box-shadow:0 2px 6px rgba(0,0,0,.3)">INICIO</div>`,
    iconAnchor: [22,10]
});
const endIcon = L.divIcon({
    className: '',
    html: `<div style="margin-left:23px;width:30px;text-align:center;background:#e8490f;color:#fff;font-size:9px;font-weight:700;font-family:'Figtree',sans-serif;padding:2px 2px 2px 2px;border-radius:5px;white-space:nowrap;box-shadow:0 2px 6px rgba(0,0,0,.3)">FIN</div>`,
    iconAnchor: [10,10]
});

// ── HELPERS ───────────────────────────────────────────────────
function toast(msg, type = '') {
    const el = document.getElementById('trk-toast');
    el.textContent = msg;
    el.className = `trk-toast show ${type}`;
    clearTimeout(el._t);
    el._t = setTimeout(() => el.className = 'trk-toast', 3500);
}
function setLoading(v) {
    document.getElementById('trk-loading').classList.toggle('show', v);
}
function fmtDate(str) {
    if (!str) return '–';
    return str.substring(0,16).replace('T',' ');
}

// ── ASSIGN USER ───────────────────────────────────────────────
async function assignUser(btn, deviceId) {
    const wrap     = btn.closest('.trk-assign-wrap');
    const select   = wrap.querySelector('.trk-assign-select');
    const feedback = document.getElementById('trk-feedback-' + deviceId);
    const userId   = select.value;

    btn.disabled = true;
    btn.textContent = '…';
    feedback.className = 'trk-assign-feedback';

    try {
        const params = new URLSearchParams({ device_id: deviceId });
        if (userId) params.set('user_id', userId);

        const res  = await fetch('{{ route("tracking.asination") }}?' + params);
        const json = await res.json();

        if (json.status) {
            feedback.textContent  = '✓ Guardado';
            feedback.className    = 'trk-assign-feedback ok';
            // Actualiza el nombre visible en la card
            const card    = wrap.closest('.trk-card');
            let userEl    = card.querySelector('.trk-card-user');
            const selText = select.options[select.selectedIndex]?.text ?? '';
            if (userId && selText) {
                card.dataset.user = selText;
                if (!userEl) {
                    userEl = document.createElement('div');
                    userEl.className = 'trk-card-user';
                    card.querySelector('.trk-card-serial').insertAdjacentElement('afterend', userEl);
                }
                userEl.textContent = '👤 ' + selText;
            } else if (userEl) {
                userEl.remove();
                card.dataset.user = '';
            }
            // Recarga la página después de 1s para que los selects se actualicen
            setTimeout(() => location.reload(), 1000);
        } else {
            feedback.textContent = json.message || 'Error';
            feedback.className   = 'trk-assign-feedback err';
        }
    } catch(e) {
        feedback.textContent = 'Error de conexión';
        feedback.className   = 'trk-assign-feedback err';
    } finally {
        btn.disabled    = false;
        btn.textContent = '✓';
        setTimeout(() => { feedback.className = 'trk-assign-feedback'; }, 3000);
    }
}

// ── MAP STYLE TOGGLE ──────────────────────────────────────────
document.querySelectorAll('[data-style]').forEach(btn => {
    btn.addEventListener('click', () => {
        const s = btn.dataset.style;
        if (s === currentStyle) return;
        map.removeLayer(tileLayers[currentStyle]);
        tileLayers[s].addTo(map);
        currentStyle = s;
        document.querySelectorAll('[data-style]').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    });
});

// ── TABS ──────────────────────────────────────────────────────
document.querySelectorAll('[data-trk-tab]').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('[data-trk-tab]').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.trk-panel').forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById('trk-panel-' + tab.dataset.trkTab).classList.add('active');
    });
});

// ── MOBILE SIDEBAR ────────────────────────────────────────────
const sidebar = document.getElementById('trk-sidebar');
const overlay = document.getElementById('trk-sb-overlay');
document.getElementById('trk-toggle-sb').addEventListener('click', () => sidebar.classList.toggle('open'));
overlay.addEventListener('click', () => sidebar.classList.remove('open'));

// ── REAL-TIME POLLING ─────────────────────────────────────────
async function fetchRealtime() {
    try {
        const res  = await fetch('{{ route("tracking.realtime") }}');
        const json = await res.json();
        if (!json.status) return;

        json.data.forEach(d => {
            if (realtimeMarkers[d.device_id]) {
                realtimeMarkers[d.device_id].setLatLng([d.lat, d.lng]);
                realtimeMarkers[d.device_id]._trkData = d;
            } else {
                const m = L.marker([d.lat, d.lng], { icon: makeIcon('live') })
                    .addTo(map)
                    .on('click', () => showInfo(d));
                m._trkData = d;
                realtimeMarkers[d.device_id] = m;
            }
            const card = document.querySelector(`.trk-card[data-id="${d.device_id}"]`);
            if (card) {
                card.dataset.lat     = d.lat;
                card.dataset.lng     = d.lng;
                card.dataset.battery = d.battery ?? '–';
                card.dataset.updated = d.updated_at ?? '–';
            }
            if (selectedDevice === d.device_id) showInfo(d);
        });

        const upEl = document.getElementById('trk-last-update');
        upEl.textContent = 'Actualizado: ' + new Date().toLocaleTimeString('es-CO', {hour:'2-digit',minute:'2-digit',second:'2-digit'});
        upEl.style.display = '';
    } catch(e) { /* silent */ }
}

// ── INFO PANEL ────────────────────────────────────────────────
function showInfo(d) {
    selectedDevice = d.device_id;
    document.getElementById('trk-ip-title').textContent = d.label || 'Dispositivo';
    const userEl = document.getElementById('trk-ip-user');
    if (d.user_name) { userEl.textContent = '👤 ' + d.user_name; userEl.style.display = ''; }
    else { userEl.style.display = 'none'; }

    const bat      = d.battery;
    const batColor = bat == null ? '' : bat > 50 ? '#4ade80' : bat > 20 ? '#fbbf24' : '#f87171';
    const rows = [
        ['Batería',   bat != null ? `<span style="color:${batColor};font-weight:700">${bat}%</span>` : '–'],
        ['Tipo',      d.tipo_label   || '–'],
        ['Red',       d.net_label    || '–'],
        ['Señal',     d.signal_label || '–'],
        ['Actualiz.', fmtDate(d.updated_at)],
    ];
    document.getElementById('trk-ip-rows').innerHTML = rows.map(([k,v]) =>
        `<div class="trk-info-row"><span class="trk-ik">${k}</span><span class="trk-iv">${v}</span></div>`
    ).join('');
    document.getElementById('trk-info-panel').classList.add('visible');
}

// ── DEVICE CARD CLICK ─────────────────────────────────────────
document.querySelectorAll('.trk-card').forEach(card => {
    card.addEventListener('click', (e) => {
        // No disparar si el click fue dentro del área de asignación
        if (e.target.closest('.trk-assign-wrap')) return;
        document.querySelectorAll('.trk-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        const lat = parseFloat(card.dataset.lat);
        const lng = parseFloat(card.dataset.lng);
        if (!isNaN(lat) && !isNaN(lng)) {
            map.flyTo([lat, lng], 16, { duration: 1 });
            showInfo({
                device_id:  card.dataset.id,
                label:      card.dataset.label,
                user_name:  card.dataset.user || null,
                battery:    card.dataset.battery === '–' ? null : parseInt(card.dataset.battery),
                updated_at: card.dataset.updated,
                tipo_label: '–', net_label: '–', signal_label: '–'
            });
        }
        if (window.innerWidth < 768) sidebar.classList.remove('open');
    });
});

// ── SEARCH ────────────────────────────────────────────────────
document.getElementById('trk-search').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.trk-card').forEach(c => {
        const match = c.dataset.label.toLowerCase().includes(q)
                   || c.dataset.serial.toLowerCase().includes(q)
                   || (c.dataset.user || '').toLowerCase().includes(q);
        c.style.display = match ? '' : 'none';
    });
});

// ── HISTORY ───────────────────────────────────────────────────
document.getElementById('trk-btn-history').addEventListener('click', loadHistory);

function calcKm(points) {
    let total = 0;
    for (let i = 1; i < points.length; i++) {
        const R    = 6371;
        const dLat = (points[i].lat - points[i-1].lat) * Math.PI / 180;
        const dLng = (points[i].lng - points[i-1].lng) * Math.PI / 180;
        const a    = Math.sin(dLat/2)**2
                   + Math.cos(points[i-1].lat*Math.PI/180)
                   * Math.cos(points[i].lat*Math.PI/180)
                   * Math.sin(dLng/2)**2;
        total += R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }
    return total.toFixed(1);
}

async function loadHistory() {
    const deviceId = document.getElementById('trk-hist-device').value;
    const day      = document.getElementById('trk-hist-day').value;
    if (!day) { toast('Selecciona un día', 'error'); return; }

    clearHistory();
    setLoading(true);
    try {
        const params = new URLSearchParams({ date: day });
        if (deviceId) params.set('device_id', deviceId);
        const res  = await fetch(`{{ route('tracking.history') }}?${params}`);
        const json = await res.json();
        if (!res.ok || !json.status) { toast(json.message || 'Error al cargar', 'error'); return; }

        const { data } = json;
        historyPoints = data.points;
        if (!historyPoints.length) { toast('Sin datos para ese día', 'error'); return; }

        const latlngs = historyPoints.map(p => [p.lat, p.lng]);
        historyLayer  = L.layerGroup();
        const poly    = L.polyline(latlngs, { color: '#6f42c1', weight: 3, opacity: .85, smoothFactor: 1 });
        historyLayer.addLayer(poly);
        historyLayer.addLayer(L.marker(latlngs[0],                  { icon: startIcon }));
        historyLayer.addLayer(L.marker(latlngs[latlngs.length - 1], { icon: endIcon }));
        const playerMarker = L.marker(latlngs[0], { icon: makeIcon('history') });
        historyLayer.addLayer(playerMarker);
        historyLayer._playerMarker = playerMarker;
        historyLayer.addTo(map);
        map.fitBounds(poly.getBounds(), { padding: [50, 50] });

        const dayFmt = new Date(day + 'T12:00:00').toLocaleDateString('es-CO', { weekday:'short', day:'numeric', month:'short' });
        document.getElementById('trk-hist-info').style.display = '';
        document.getElementById('trk-hist-info').innerHTML = `<strong>${data.device || 'Todos'}</strong><br>${dayFmt}`;
        document.getElementById('trk-stat-pts').textContent = historyPoints.length;
        document.getElementById('trk-stat-km').textContent  = calcKm(historyPoints);
        document.getElementById('trk-hist-stats').style.display = '';

        const slider = document.getElementById('trk-rp-slider');
        slider.max   = historyPoints.length - 1;
        slider.value = 0;
        document.getElementById('trk-rp-total').textContent = historyPoints.length + ' puntos';
        updatePlayer(0);
        document.getElementById('trk-route-player').classList.add('visible');
        toast(`${historyPoints.length} pts · ${calcKm(historyPoints)} km`, 'success');
    } catch(e) {
        toast('Error de conexión', 'error');
    } finally {
        setLoading(false);
    }
}

function updatePlayer(idx) {
    const p = historyPoints[idx];
    if (!p || !historyLayer) return;
    historyLayer._playerMarker?.setLatLng([p.lat, p.lng]);
    document.getElementById('trk-rp-current').textContent = fmtDate(p.at);
}
document.getElementById('trk-rp-slider').addEventListener('input', function() { updatePlayer(parseInt(this.value)); });

function clearHistory() {
    if (historyLayer) { map.removeLayer(historyLayer); historyLayer = null; }
    historyPoints = [];
    document.getElementById('trk-route-player').classList.remove('visible');
    document.getElementById('trk-hist-info').style.display  = 'none';
    document.getElementById('trk-hist-stats').style.display = 'none';
}
document.getElementById('trk-btn-clear').addEventListener('click', clearHistory);
document.getElementById('trk-rp-close').addEventListener('click',  clearHistory);

// ── INIT ──────────────────────────────────────────────────────
fetchRealtime();
setInterval(fetchRealtime, 15000);

const initBounds = [];
document.querySelectorAll('.trk-card').forEach(c => {
    const lat = parseFloat(c.dataset.lat);
    const lng = parseFloat(c.dataset.lng);
    if (!isNaN(lat) && !isNaN(lng)) initBounds.push([lat, lng]);
});
if (initBounds.length === 1)    map.setView(initBounds[0], 15);
else if (initBounds.length > 1) map.fitBounds(L.latLngBounds(initBounds), { padding:[60,60], maxZoom:14 });
</script>
@endsection
