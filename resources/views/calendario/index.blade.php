@extends('layouts.app')
@section('content')

    {{-- ══════════════════════════════════════════════
         ESTILOS PERSONALIZADOS
    ══════════════════════════════════════════════ --}}
    <style>
        /* ---- Tarjetas de mes ---- */
        .mes-card {
            transition: transform .18s ease, box-shadow .18s ease;
            cursor: pointer;
        }
        .mes-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,.13);
        }

        /* ---- Leyenda badges ---- */
        .badge-trabajado  { background:#fff;    color:#111;    border:1px solid #d1d5db; }
        .badge-sabado     { background:#fff;    color:#3b82f6; border:1px solid #bfdbfe; }
        .badge-domingo    { background:#fff;    color:#ef4444; border:1px solid #fecaca; }
        .badge-festivo    { background:#fed7aa; color:#111;    border:1px solid #fdba74; }
        .badge-no-labora  { background:#e9d5ff; color:#6b21a8; border:1px solid #d8b4fe; }

        /* ---- Celdas del calendario modal ---- */
        .cal-day {
            min-height: 72px;
            border-radius: 8px;
            padding: 6px 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: .85rem;
            position: relative;
            transition: box-shadow .12s;
        }

        /* Colores por tipo */
        .cal-day.tipo-trabajado  { background:#fff;    color:#111;    border:1px solid #e5e7eb; }
        .cal-day.tipo-sabado     { background:#fff;    color:#3b82f6; border:1px solid #bfdbfe; }
        .cal-day.tipo-domingo    { background:#fff;    color:#ef4444; border:1px solid #fecaca; }
        .cal-day.tipo-festivo    { background:#fed7aa; color:#111;    border:1px solid #fdba74; }
        .cal-day.tipo-no_labora  { background:#e9d5ff; color:#6b21a8; border:1px solid #d8b4fe; }
        .cal-day.tipo-empty      { background:transparent; border:none; }

        /* Días pasados → fondo gris semitransparente */
        .cal-day.is-past-or-today::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 8px;
            background: rgba(209,213,219,.45);
            pointer-events: none;
        }

        /* Hoy: borde azul */
        .cal-day.is-today {
            outline: 2px solid #3b82f6;
            outline-offset: 1px;
        }

        /* Número del día */
        .cal-day .day-num {
            font-size: 1.1rem;
            font-weight: 800;
            line-height: 1;
        }
        /* Nombre del día */
        .cal-day .day-name {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .04em;
            margin-top: 2px;
        }
        /* Etiqueta festivo / no labora (comentario) */
        .cal-day .day-label {
            font-size: .63rem;
            text-align: center;
            margin-top: 3px;
            line-height: 1.25;
            word-break: break-word;
            font-weight: 600;
            max-width: 100%;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        /* ---- Botones acción ---- */
        .btn-accion {
            margin-top: auto;
            font-size: .65rem;
            padding: 2px 4px;
            border-radius: 4px;
            border: none;
            background: transparent;
            cursor: pointer;
            font-weight: 700;
            white-space: nowrap;
            position: relative;
            z-index: 1;
        }
        .btn-marcar { color: #7c3aed; }
        .btn-quitar { color: #dc2626; }
        .btn-marcar:hover { text-decoration: underline; color: #6d28d9; }
        .btn-quitar:hover { text-decoration: underline; color: #b91c1c; }

        /* ---- Tooltip custom ---- */
        [data-tooltip] { position: relative; }
        [data-tooltip]::before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: calc(100% + 4px);
            left: 50%;
            transform: translateX(-50%);
            background: #1e293b;
            color: #f8fafc;
            font-size: .6rem;
            font-weight: 600;
            padding: 3px 7px;
            border-radius: 5px;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transition: opacity .15s;
            z-index: 50;
        }
        [data-tooltip]:hover::before { opacity: 1; }

        /* Header días semana */
        .cal-header-day {
            text-align: center;
            font-size: .78rem;
            font-weight: 800;
            padding: 5px 0;
            letter-spacing: .05em;
            color: #6b7280;
        }

        /* Spinner */
        #cal-loading {
            display: none;
            justify-content: center;
            align-items: center;
            min-height: 220px;
        }

        /* Navegación año */
        .year-nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 14px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background: #fff;
            font-size: .82rem;
            font-weight: 700;
            color: #374151;
            cursor: pointer;
            transition: background .15s, border-color .15s;
        }
        .year-nav-btn:hover { background: #f3f4f6; border-color: #9ca3af; }
        .dark .year-nav-btn { background: #1f2937; border-color: #374151; color: #e5e7eb; }
        .dark .year-nav-btn:hover { background: #374151; }

        /* Sabados/2 texto */
        .sabados-media {
            font-size: .7rem;
            color: #3b82f6;
            font-weight: 600;
            margin-left: 4px;
        }
    </style>

    {{-- ══════════════════════════════════════════════
         CABECERA: título + navegación de año
    ══════════════════════════════════════════════ --}}
    <div class="py-6 px-4 mx-auto">

        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            {{-- Navegación año --}}
            <div class="flex items-center gap-3">
                <button class="year-nav-btn" onclick="cambiarAnio(-1)" data-tooltip="Ver año anterior">
                    ← <span id="lbl-anio-ant">{{ $year - 1 }}</span>
                </button>
                <span id="lbl-anio-actual" class="text-xl font-extrabold text-gray-800 dark:text-gray-100 px-2">
                    {{ $year }}
                </span>
                <button class="year-nav-btn" onclick="cambiarAnio(1)" data-tooltip="Ver año siguiente">
                    <span id="lbl-anio-sig">{{ $year + 1 }}</span> →
                </button>
            </div>

            {{-- Leyenda global --}}
            <div class="flex flex-wrap gap-2">
                <span class="badge-trabajado px-3 py-1 rounded-full text-xs font-semibold">Laborable</span>
                <span class="badge-sabado    px-3 py-1 rounded-full text-xs font-semibold">Sábado</span>
                <span class="badge-domingo   px-3 py-1 rounded-full text-xs font-semibold">Domingo</span>
                <span class="badge-festivo   px-3 py-1 rounded-full text-xs font-semibold">Festivo</span>
                <span class="badge-no-labora px-3 py-1 rounded-full text-xs font-semibold">No labora</span>
            </div>
        </div>

        {{-- Grid 12 meses --}}
        <div id="grid-meses" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach(range(1, 12) as $m)
                @php
                    $nombres = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
                                'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                    $r = $meses[$m];
                @endphp
                <div
                    class="mes-card bg-white dark:bg-gray-800 rounded-xl shadow p-4 border border-gray-100 dark:border-gray-700"
                    data-mes="{{ $m }}"
                    onclick="abrirMes(_anioActual, {{ $m }}, '{{ $nombres[$m] }}')"
                >
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-bold text-gray-800 dark:text-gray-100">{{ $nombres[$m] }}</h3>
                        <span class="mes-anio-label text-xs text-gray-400 dark:text-gray-500">{{ $year }}</span>
                    </div>

                    <div class="space-y-1 text-xs mes-resumen">
                        {{-- Laborables + sábados/2 --}}
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">Laborables</span>
                            <span class="font-bold text-gray-800 dark:text-gray-100 flex items-center gap-1">
                                <span class="val-trabajados">{{ $r['trabajados'] }}</span>
                                @if($r['sabados'] > 0)
                                    <span class="sabados-media val-sabados-media">+ {{ $r['sabadosMedia'] }}</span>
                                @else
                                    <span class="sabados-media val-sabados-media hidden"></span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span style="color:#3b82f6;">Sábados</span>
                            <span class="font-bold val-sabados" style="color:#3b82f6;">{{ $r['sabados'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span style="color:#ef4444;">Domingos</span>
                            <span class="font-bold val-domingos" style="color:#ef4444;">{{ $r['domingos'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span style="color:#ea580c;">Festivos</span>
                            <span class="font-bold val-festivos" style="color:#ea580c;">{{ $r['festivos'] }}</span>
                        </div>
                        <div class="flex justify-between items-center {{ $r['noLabora'] == 0 ? 'hidden' : '' }} row-no-labora">
                            <span style="color:#7c3aed;">No labora</span>
                            <span class="font-bold val-no-labora" style="color:#7c3aed;">{{ $r['noLabora'] }}</span>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 text-center text-xs text-gray-400">
                        Ver calendario →
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         MODAL DEL MES
    ══════════════════════════════════════════════ --}}
    <x-modal name="modal-mes" maxWidth="5xl">
        <div class="p-5">
            {{-- Título --}}
            <div class="flex items-center justify-between mb-4">
                <h3 id="modal-titulo" class="text-lg font-bold text-gray-800 dark:text-gray-100">Cargando...</h3>
                <button
                    type="button"
                    onclick="cerrarModal()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Spinner --}}
            <div id="cal-loading">
                <svg class="animate-spin w-10 h-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
            </div>

            {{-- Leyenda modal --}}
            <div id="cal-leyenda" class="hidden flex flex-wrap gap-2 mb-4 text-xs">
                <span class="badge-trabajado px-2 py-1 rounded-full font-semibold">Laborable</span>
                <span class="badge-sabado    px-2 py-1 rounded-full font-semibold">Sábado</span>
                <span class="badge-domingo   px-2 py-1 rounded-full font-semibold">Domingo</span>
                <span class="badge-festivo   px-2 py-1 rounded-full font-semibold">Festivo</span>
                <span class="badge-no-labora px-2 py-1 rounded-full font-semibold">No labora</span>
            </div>

            {{-- Cabecera días --}}
            <div id="cal-header" class="hidden grid grid-cols-7 gap-1 mb-1">
                @foreach(['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'] as $dn)
                    <div class="cal-header-day">{{ $dn }}</div>
                @endforeach
            </div>

            {{-- Grilla --}}
            <div id="cal-grid" class="grid grid-cols-7 gap-1"></div>

            {{-- Resumen pie --}}
            <div id="cal-resumen" class="hidden mt-4 pt-3 border-t border-gray-100 dark:border-gray-700">
                <div class="flex flex-wrap gap-3 text-xs justify-center" id="cal-resumen-items"></div>
            </div>
        </div>
    </x-modal>

    {{-- ══════════════════════════════════════════════
         JAVASCRIPT
    ══════════════════════════════════════════════ --}}
    <script>
    // ─── Estado global ─────────────────────────────────────────────────
    let _mesActual   = null;
    let _anioActual  = {{ $year }};
    let _huboCambios = false;   // ← bandera: si se marcó/quitó algún día no laboral

    const MESES_NOMBRES = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
                            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

    // ─── Cerrar modal (botón X interno) ──────────────────────────────
    function cerrarModal() {
        window.dispatchEvent(new CustomEvent('close-modal', { detail: 'modal-mes' }));
        if (_huboCambios) {
            window.location.reload();
        }
        _huboCambios = false;
    }

    // ─── Actualizar una tarjeta específica del índice vía AJAX ────────
    function actualizarTarjetaMes(year, month) {
        fetch(`{{ route('calendario.resumenAnio') }}?year=${year}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            const r    = data[month];
            const card = document.querySelector(`.mes-card[data-mes="${month}"]`);
            if (!card || !r) return;

            card.querySelector('.val-trabajados').textContent = r.trabajados;
            card.querySelector('.val-sabados').textContent    = r.sabados;
            card.querySelector('.val-domingos').textContent   = r.domingos;
            card.querySelector('.val-festivos').textContent   = r.festivos;
            card.querySelector('.val-no-labora').textContent  = r.noLabora;

            // Sábados/2
            const spanMedia = card.querySelector('.val-sabados-media');
            if (r.sabados > 0) {
                spanMedia.textContent = '+ ' + r.sabadosMedia;
                spanMedia.classList.remove('hidden');
            } else {
                spanMedia.textContent = '';
                spanMedia.classList.add('hidden');
            }

            // Mostrar/ocultar fila no labora
            const rowNoLabora = card.querySelector('.row-no-labora');
            if (r.noLabora > 0) {
                rowNoLabora.classList.remove('hidden');
            } else {
                rowNoLabora.classList.add('hidden');
            }
        })
        .catch(() => {
            // Silencioso — no crítico
        });
    }

    // ─── Cambiar año (navegación) ──────────────────────────────────────
    function cambiarAnio(delta) {
        _anioActual += delta;

        document.getElementById('lbl-anio-actual').textContent = _anioActual;
        document.getElementById('lbl-anio-ant').textContent    = _anioActual - 1;
        document.getElementById('lbl-anio-sig').textContent    = _anioActual + 1;

        document.querySelectorAll('.mes-card').forEach(c => {
            c.querySelector('.mes-anio-label').textContent = _anioActual;
        });

        cargarResumenAnio(_anioActual);
    }

    // ─── Cargar resumen anual vía AJAX ────────────────────────────────
    function cargarResumenAnio(year) {
        Swal.fire({
            title: 'Cargando ' + year + '...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading(),
        });

        fetch(`{{ route('calendario.resumenAnio') }}?year=${year}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            Swal.close();
            for (let m = 1; m <= 12; m++) {
                const r    = data[m];
                const card = document.querySelector(`.mes-card[data-mes="${m}"]`);
                if (!card || !r) continue;

                card.querySelector('.val-trabajados').textContent = r.trabajados;
                card.querySelector('.val-sabados').textContent    = r.sabados;
                card.querySelector('.val-domingos').textContent   = r.domingos;
                card.querySelector('.val-festivos').textContent   = r.festivos;
                card.querySelector('.val-no-labora').textContent  = r.noLabora;

                const spanMedia = card.querySelector('.val-sabados-media');
                if (r.sabados > 0) {
                    spanMedia.textContent = '+ ' + r.sabadosMedia;
                    spanMedia.classList.remove('hidden');
                } else {
                    spanMedia.textContent = '';
                    spanMedia.classList.add('hidden');
                }

                const rowNoLabora = card.querySelector('.row-no-labora');
                if (r.noLabora > 0) {
                    rowNoLabora.classList.remove('hidden');
                } else {
                    rowNoLabora.classList.add('hidden');
                }

                card.setAttribute('onclick',
                    `abrirMes(${year}, ${m}, '${MESES_NOMBRES[m]}')`);
            }
        })
        .catch(() => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cargar el resumen del año.' });
        });
    }

    // ─── Abrir modal de un mes ─────────────────────────────────────────
    function abrirMes(year, month, nombre) {
        _mesActual   = month;
        _anioActual  = year;
        _huboCambios = false;   // reiniciar bandera al abrir

        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-mes' }));

        document.getElementById('modal-titulo').textContent = nombre + ' ' + year;
        document.getElementById('cal-grid').innerHTML       = '';
        document.getElementById('cal-resumen').classList.add('hidden');
        document.getElementById('cal-leyenda').classList.add('hidden');
        document.getElementById('cal-header').classList.add('hidden');
        document.getElementById('cal-loading').style.display = 'flex';

        fetch(`{{ route('calendario.mesDatos') }}?year=${year}&month=${month}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => renderizarMes(data))
        .catch(() => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar los datos del mes.' });
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'modal-mes' }));
        })
        .finally(() => {
            document.getElementById('cal-loading').style.display = 'none';
        });
    }

    // ─── Renderizar grilla del mes ────────────────────────────────────
    function renderizarMes(data) {
        const grid  = document.getElementById('cal-grid');
        const items = document.getElementById('cal-resumen-items');
        grid.innerHTML = '';

        document.getElementById('cal-leyenda').classList.remove('hidden');
        document.getElementById('cal-header').classList.remove('hidden');

        // Celdas vacías de offset
        for (let i = 0; i < data.primerDia; i++) {
            const empty = document.createElement('div');
            empty.className = 'cal-day tipo-empty';
            grid.appendChild(empty);
        }

        data.dias.forEach(d => {
            const cell = document.createElement('div');
            cell.className = `cal-day tipo-${d.tipo}`;

            if (d.isToday)     cell.classList.add('is-today', 'is-past-or-today');
            else if (d.isPast) cell.classList.add('is-past-or-today');

            // Número
            const num = document.createElement('span');
            num.className   = 'day-num';
            num.textContent = d.day;
            cell.appendChild(num);

            // Nombre del día
            const nombre = document.createElement('span');
            nombre.className   = 'day-name';
            nombre.textContent = d.dayName;
            cell.appendChild(nombre);

            // Etiqueta: para festivos → nombre; para no_labora → comentario
            if (d.festivoName) {
                const lbl = document.createElement('span');
                lbl.className   = 'day-label';
                lbl.textContent = d.festivoName;
                cell.appendChild(lbl);
            }

            // Botón acción (solo si es editable)
            if (d.editable) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn-accion';

                if (d.noLabora) {
                    btn.classList.add('btn-quitar');
                    btn.textContent = '✕ Quitar día no laboral';
                    btn.setAttribute('data-tooltip', 'Revertir a día laborable');
                    btn.onclick = (e) => { e.stopPropagation(); confirmarQuitarNoLaboral(d.date); };
                } else {
                    btn.classList.add('btn-marcar');
                    btn.textContent = '＋ Marcar no laboral';
                    btn.setAttribute('data-tooltip', 'Marcar como día no laboral');
                    btn.onclick = (e) => { e.stopPropagation(); confirmarMarcarNoLaboral(d.date, d.dayName); };
                }

                cell.appendChild(btn);
            }

            grid.appendChild(cell);
        });

        // Resumen pie
        const conteo = { trabajado: 0, sabado: 0, domingo: 0, festivo: 0, no_labora: 0 };
        data.dias.forEach(d => { if (conteo[d.tipo] !== undefined) conteo[d.tipo]++; });

        const sabadosMedia = conteo.sabado > 0 ? (conteo.sabado / 2).toFixed(1).replace(/\.0$/, '') : 0;
        const mediaTexto   = conteo.sabado > 0
            ? `<span style="color:#3b82f6;font-size:.7rem;margin-left:3px;">+ ${sabadosMedia}</span>`
            : '';

        items.innerHTML = `
            <span class="badge-trabajado px-3 py-1 rounded-full font-semibold">
                Laborables: ${conteo.trabajado} ${mediaTexto}
            </span>
            <span class="badge-sabado    px-3 py-1 rounded-full font-semibold">Sábados: ${conteo.sabado}</span>
            <span class="badge-domingo   px-3 py-1 rounded-full font-semibold">Domingos: ${conteo.domingo}</span>
            <span class="badge-festivo   px-3 py-1 rounded-full font-semibold">Festivos: ${conteo.festivo}</span>
            ${conteo.no_labora > 0
                ? `<span class="badge-no-labora px-3 py-1 rounded-full font-semibold">No labora: ${conteo.no_labora}</span>`
                : ''}
        `;
        document.getElementById('cal-resumen').classList.remove('hidden');
    }

    // ─── Confirmar marcar día no laboral (con comentario obligatorio) ──
    function confirmarMarcarNoLaboral(fecha, nombreDia) {
        Swal.fire({
            title: '¿Marcar como día no laboral?',
            html: `
                <p style="margin-bottom:10px;font-size:.9rem;color:#4b5563;">
                    <b>${formatearFecha(fecha)}</b> (${nombreDia}) se marcará como día no laboral.
                </p>
                <textarea
                    id="swal-comentario"
                    maxlength="255"
                    autocomplete="off"
                    placeholder="Escribe el motivo o comentario (obligatorio)"
                    style="
                        width: 100%;
                        min-height: 110px;
                        padding: 10px 12px;
                        border: 1px solid #d1d5db;
                        border-radius: 8px;
                        font-size: .9rem;
                        resize: vertical;
                        outline: none;
                        box-sizing: border-box;
                        font-family: inherit;
                        transition: border-color .15s;
                    "
                    onfocus="this.style.borderColor='#7c3aed'"
                    onblur="this.style.borderColor='#d1d5db'"
                ></textarea>
                <p style="text-align:right;font-size:.72rem;color:#9ca3af;margin-top:4px;">
                    <span id="swal-char-count">0</span>/255
                </p>
                <script>
                    document.getElementById('swal-comentario')
                        .addEventListener('input', function() {
                            document.getElementById('swal-char-count').textContent = this.value.length;
                        });
                <\/script>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Guardar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#7c3aed',
            cancelButtonColor: '#9ca3af',
            focusConfirm: false,
            preConfirm: () => {
                const comentario = document.getElementById('swal-comentario').value.trim();
                if (!comentario) {
                    Swal.showValidationMessage('El comentario es obligatorio.');
                    return false;
                }
                return comentario;
            },
        }).then(result => {
            if (!result.isConfirmed) return;

            const comentario = result.value;

            Swal.fire({
                title: 'Guardando...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading(),
            });

            fetch('{{ route('calendario.marcarNoLaboral') }}', {
                method: 'POST',
                headers: {
                    'Content-Type':     'application/json',
                    'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ date: fecha, comentario }),
            })
            .then(async r => {
                const json = await r.json();
                if (!r.ok) throw new Error(json.error || 'Error al guardar.');
                return json;
            })
            .then(() => {
                _huboCambios = true;
                Swal.fire({
                    icon: 'success',
                    title: '¡Listo!',
                    text: 'Día marcado como no laboral.',
                    timer: 1500,
                    showConfirmButton: false,
                }).then(() => recargarMesActual());
            })
            .catch(err => Swal.fire({ icon: 'error', title: 'Error', text: err.message }));
        });
    }

    // ─── Confirmar quitar día no laboral ─────────────────────────────
    function confirmarQuitarNoLaboral(fecha) {
        Swal.fire({
            title: '¿Quitar día no laboral?',
            html: `<b>${formatearFecha(fecha)}</b> volverá a ser un día laborable.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, quitar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#9ca3af',
        }).then(result => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Procesando...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading(),
            });

            fetch('{{ route('calendario.quitarNoLaboral') }}', {
                method: 'POST',
                headers: {
                    'Content-Type':     'application/json',
                    'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ date: fecha }),
            })
            .then(async r => {
                const json = await r.json();
                if (!r.ok) throw new Error(json.error || 'Error al procesar.');
                return json;
            })
            .then(() => {
                _huboCambios = true;
                Swal.fire({
                    icon: 'success',
                    title: '¡Listo!',
                    text: 'Día no laboral eliminado.',
                    timer: 1500,
                    showConfirmButton: false,
                }).then(() => recargarMesActual());
            })
            .catch(err => Swal.fire({ icon: 'error', title: 'Error', text: err.message }));
        });
    }

    // ─── Recargar mes activo dentro del modal ─────────────────────────
    function recargarMesActual() {
        if (!_mesActual || !_anioActual) return;

        document.getElementById('cal-grid').innerHTML = '';
        document.getElementById('cal-resumen').classList.add('hidden');
        document.getElementById('cal-leyenda').classList.add('hidden');
        document.getElementById('cal-header').classList.add('hidden');
        document.getElementById('cal-loading').style.display = 'flex';

        fetch(`{{ route('calendario.mesDatos') }}?year=${_anioActual}&month=${_mesActual}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => renderizarMes(data))
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron recargar los datos.' }))
        .finally(() => { document.getElementById('cal-loading').style.display = 'none'; });
    }

    // ─── Helper: fecha legible ────────────────────────────────────────
    function formatearFecha(dateStr) {
        const [y, m, d] = dateStr.split('-').map(Number);
        return `${d} de ${MESES_NOMBRES[m]} de ${y}`;
    }
    </script>

@endsection
