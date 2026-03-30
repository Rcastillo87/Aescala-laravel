{{-- ══════════════════════════════════════════════════════════════════
     ESTILOS CALENDARIO PROYECTO
     Agregar en @push('styles') o dentro de la vista principal
══════════════════════════════════════════════════════════════════ --}}
<style>

/* ── Wrapper de cada mes ──────────────────────────────────────────── */
.cp-mes-wrapper {
    margin-bottom: 28px;
}
.cp-mes-header {
    font-size: .82rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #374151;
    padding: 6px 4px 4px;
    border-bottom: 2px solid #e5e7eb;
    margin-bottom: 6px;
}
.dark .cp-mes-header { color: #d1d5db; border-color: #374151; }

/* ── Grilla 7 columnas ────────────────────────────────────────────── */
.cp-semana-header,
.cp-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 3px;
}
.cp-header-day {
    text-align: center;
    font-size: .68rem;
    font-weight: 800;
    padding: 3px 0;
    letter-spacing: .05em;
    color: #9ca3af;
}

/* ── Celda base ───────────────────────────────────────────────────── */
.cp-day {
    min-height: 70px;
    border-radius: 8px;
    padding: 5px 3px 4px;
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: .8rem;
    position: relative;
    overflow: hidden;
    transition: box-shadow .12s;
}
.cp-empty { background: transparent !important; border: none !important; min-height: 70px; }

.cp-day-num  { font-size: 1rem;  font-weight: 800; line-height: 1; }
.cp-day-name { font-size: .65rem; font-weight: 700; letter-spacing: .04em; margin-top: 2px; opacity: .8; }
.cp-day-label {
    font-size: .58rem; font-weight: 600; text-align: center;
    margin-top: 3px; line-height: 1.2; word-break: break-word;
    max-width: 100%;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.cp-btn-marcar {
    margin-top: auto;
    font-size: .58rem; padding: 2px 3px; border-radius: 4px;
    border: none; background: transparent; color: #d97706;
    cursor: pointer; font-weight: 700; white-space: nowrap;
    position: relative; z-index: 1;
}
.cp-btn-marcar:hover { text-decoration: underline; color: #b45309; }

/* ══════════════════════════════════════════════════════════════════
   COLORES POR TIPO  ×  ESTADO (pasado / hoy / futuro)

   Convención de clases: cp-tipo-{tipo} + cp-estado-{estado}
   El estado modifica opacidad y borde del tipo.
══════════════════════════════════════════════════════════════════ */

/* ── LABORABLE ────────────────────────────────────────────────────── */
.cp-tipo-laborable {
    background: #16a34a;
    color: #ffffff;
    border: 1px solid #15803d;
    font-weight: 600;
}
/* Pasado: ligeramente lavado + tachado sutil */
.cp-tipo-laborable.cp-estado-pasado {
    background: #4ade80;
    color: #14532d;
    border-color: #86efac;
    opacity: .8;
}
/* Hoy: borde grueso verde intenso + sombra */
.cp-tipo-laborable.cp-estado-hoy {
    background: #15803d;
    border: 2px solid #052e16;
    box-shadow: 0 0 0 3px #16a34a80;
    color: #ffffff;
    font-weight: 800;
}
/* Futuro: color base limpio */
.cp-tipo-laborable.cp-estado-futuro {
    background: #f3f4f6;
    color: #9ca3af;
    border: 1px dashed #d1d5db;
    opacity: .7;
}

/* ── SÁBADO ───────────────────────────────────────────────────────── */
/* Sin línea diagonal — solo gradiente de fondo: blanco arriba, verde abajo */
.cp-tipo-sabado {
    background: linear-gradient(to bottom, #f0fdf4 50%, #16a34a 50%);
    color: #14532d;
    border: 1px solid #16a34a;
    font-weight: 600;
}
.cp-tipo-sabado.cp-estado-pasado {
    background: linear-gradient(to bottom, #dcfce7 50%, #4ade80 50%);
    color: #14532d;
    border-color: #86efac;
    opacity: .8;
}
.cp-tipo-sabado.cp-estado-hoy {
    background: linear-gradient(to bottom, #f0fdf4 50%, #15803d 50%);
    border: 2px solid #052e16;
    box-shadow: 0 0 0 3px #16a34a80;
    color: #14532d;
}
.cp-tipo-sabado.cp-estado-futuro {
    background: linear-gradient(to bottom, #f9fafb 50%, #f3f4f6 50%);
    color: #9ca3af;
    border: 1px dashed #d1d5db;
    opacity: .7;
}

/* ── DOMINGO ──────────────────────────────────────────────────────── */
.cp-tipo-domingo {
    background: #fff1f2;
    color: #be123c;
    border: 1px solid #fecdd3;
}
.cp-tipo-domingo.cp-estado-pasado  { opacity: .65; }
.cp-tipo-domingo.cp-estado-futuro  { opacity: .45; filter: grayscale(.4); }
.cp-tipo-domingo.cp-estado-hoy     { border: 2px solid #e11d48; box-shadow: 0 0 0 2px #fecdd380; }

/* ── FESTIVO ──────────────────────────────────────────────────────── */
.cp-tipo-festivo {
    background: #fff7ed;
    color: #9a3412;
    border: 1px solid #fed7aa;
}
.cp-tipo-festivo.cp-estado-pasado  { opacity: .65; }
.cp-tipo-festivo.cp-estado-hoy     { border: 2px solid #ea580c; box-shadow: 0 0 0 2px #fed7aa80; }

/* ── NO LABORAL GLOBAL ────────────────────────────────────────────── */
.cp-tipo-no_laboral_global {
    background: #f5f3ff;
    color: #6d28d9;
    border: 1px solid #ddd6fe;
}
.cp-tipo-no_laboral_global.cp-estado-pasado  { opacity: .65; }
.cp-tipo-no_laboral_global.cp-estado-hoy     { border: 2px solid #7c3aed; box-shadow: 0 0 0 2px #ddd6fe80; }

/* ── NO LABORADO DEL PROYECTO ─────────────────────────────────────── */
/* Solo aplica a pasados/hoy — color ámbar cálido */
.cp-tipo-no_laborado {
    background: #fef9c3;
    color: #854d0e;
    border: 1px solid #fde68a;
}
.cp-tipo-no_laborado.cp-estado-pasado {
    background: #fef9c3;
    opacity: .8;
}
.cp-tipo-no_laborado.cp-estado-hoy {
    border: 2px solid #d97706;
    box-shadow: 0 0 0 2px #fde68a80;
}

/* ── Badges leyenda ───────────────────────────────────────────────── */
.cp-badge-lab  { background:#16a34a; color:#fff; border:1px solid #15803d; }
.cp-badge-sab  { background:linear-gradient(to right,#f0fdf4 50%,#16a34a 50%); color:#14532d; border:1px solid #16a34a; }
.cp-badge-dom  { background:#fff1f2; color:#be123c; border:1px solid #fecdd3; }
.cp-badge-fest { background:#fff7ed; color:#9a3412; border:1px solid #fed7aa; }
.cp-badge-nolg { background:#f5f3ff; color:#6d28d9; border:1px solid #ddd6fe; }
.cp-badge-nolab{ background:#fef9c3; color:#854d0e; border:1px solid #fde68a; }

/* ── Estados en leyenda ───────────────────────────────────────────── */
.cp-badge-estado {
    display:inline-flex; align-items:center; gap:5px;
    padding:2px 8px; border-radius:20px; font-size:.7rem;
    font-weight:700; border:1px solid transparent;
}
.cp-est-pasado  { background:#f3f4f6; color:#6b7280; border-color:#e5e7eb; }
.cp-est-hoy     { background:#dbeafe; color:#1d4ed8; border-color:#93c5fd; }
.cp-est-futuro  { background:#f0fdf4; color:#15803d; border-color:#86efac; }

/* ── Spinner ──────────────────────────────────────────────────────── */
#cp-loading {
    display: none;
    justify-content: center;
    align-items: center;
    min-height: 180px;
}

/* ── Info chips cabecera ──────────────────────────────────────────── */
.cp-info-chip {
    display:inline-flex; align-items:center; gap:4px;
    padding:3px 10px; border-radius:20px;
    font-size:.74rem; font-weight:600;
    border:1px solid #e5e7eb; background:#f9fafb; color:#374151;
}
.dark .cp-info-chip { background:#1f2937; border-color:#374151; color:#d1d5db; }

/* ── Zona scrolleable de meses ────────────────────────────────────── */
#cp-meses-contenedor {
    max-height: 60vh;
    overflow-y: auto;
    padding-right: 4px;
}
#cp-meses-contenedor::-webkit-scrollbar      { width: 5px; }
#cp-meses-contenedor::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
</style>

{{-- ══════════════════════════════════════════════════════════════════
     MODAL CALENDARIO PROYECTO
══════════════════════════════════════════════════════════════════ --}}
<x-modal name="calendario-modal" maxWidth="5xl">
    <div class="p-5" id="cal-proy-panel">

        {{-- Encabezado --}}
        <div class="flex items-start justify-between mb-3 gap-3">
            <div class="flex-1 min-w-0">
                <h3 id="cp-nombre-proy"
                    class="text-base font-extrabold text-gray-800 dark:text-gray-100 truncate mb-1">
                    Cargando...
                </h3>
                <p id="cp-cliente-proy" class="text-xs text-gray-500 dark:text-gray-400 mb-2"></p>
                <div class="flex flex-wrap gap-2">
                    <span class="cp-info-chip">📅 Inicio: <b id="cp-fec-inicio">—</b></span>
                    <span class="cp-info-chip">🏁 Fin est.: <b id="cp-fec-fin-est">—</b></span>
                    <span class="cp-info-chip">⏱ Días: <b id="cp-dias-trabajo">—</b></span>
                </div>
            </div>
            <button type="button"
                x-data=""
                x-on:click="$dispatch('close-modal', 'calendario-modal')"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition flex-shrink-0 mt-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Leyenda tipos + estados ----}}
        <div id="cp-leyenda" class="hidden mb-3">
            {{-- Tipos --}}
            <div class="flex flex-wrap gap-1 mb-1 text-xs">
                <span class="cp-badge-lab  px-2 py-1 rounded-full font-semibold">Lun–Vie</span>
                <span class="cp-badge-sab  px-2 py-1 rounded-full font-semibold">Sábado ½</span>
                <span class="cp-badge-dom  px-2 py-1 rounded-full font-semibold">Domingo</span>
                <span class="cp-badge-fest px-2 py-1 rounded-full font-semibold">Festivo</span>
                <span class="cp-badge-nolg px-2 py-1 rounded-full font-semibold">No lab. global</span>
                <span class="cp-badge-nolab px-2 py-1 rounded-full font-semibold">No laborado</span>
            </div>
            {{-- Estados --}}
            <div class="flex flex-wrap gap-1 text-xs">
                <span class="cp-badge-estado cp-est-pasado">● Pasado</span>
                <span class="cp-badge-estado cp-est-hoy">● Hoy</span>
                <span class="cp-badge-estado cp-est-futuro">● Futuro</span>
            </div>
        </div>

        {{-- Spinner --}}
        <div id="cp-loading">
            <svg class="animate-spin w-9 h-9 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
        </div>

        {{-- Meses scrolleables --}}
        <div id="cp-meses-contenedor"></div>

        {{-- Resumen pie --}}
        <div id="cp-resumen" class="hidden mt-4 pt-3 border-t border-gray-100 dark:border-gray-700">
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
                <div class="cp-badge-lab  px-3 py-2 rounded-lg font-semibold flex justify-between">
                    <span>Lun–Vie</span><span id="cp-res-laborable">0</span>
                </div>
                <div class="cp-badge-sab  px-3 py-2 rounded-lg font-semibold flex justify-between">
                    <span>Sábados</span><span id="cp-res-sabado">0</span>
                </div>
                <div class="cp-badge-dom  px-3 py-2 rounded-lg font-semibold flex justify-between">
                    <span>Domingos</span><span id="cp-res-domingo">0</span>
                </div>
                <div class="cp-badge-fest px-3 py-2 rounded-lg font-semibold flex justify-between">
                    <span>Festivos</span><span id="cp-res-festivo">0</span>
                </div>
                <div id="cp-row-no-lab-global" class="cp-badge-nolg px-3 py-2 rounded-lg font-semibold flex justify-between hidden">
                    <span>No lab. global</span><span id="cp-res-no-lab-global">0</span>
                </div>
                <div id="cp-row-no-laborado" class="cp-badge-nolab px-3 py-2 rounded-lg font-semibold flex justify-between hidden">
                    <span>No laborados</span><span id="cp-res-no-laborado">0</span>
                </div>
            </div>
        </div>

    </div>
</x-modal>
