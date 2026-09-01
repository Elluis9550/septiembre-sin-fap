const btnAbrirReporte = document.getElementById('btnAbrirReporte');
const modalReporte = new bootstrap.Modal(document.getElementById('modalReporte'));
const btnConfirmarReporte = document.getElementById('btnConfirmarReporte');
const errorReporte = document.getElementById('errorReporte');
const listaParticipantes = document.getElementById('listaParticipantes');
const selectorOrden = document.getElementById('selectorOrden');

let resultadoSeleccionado = null;
let countdownInterval = null;

// -------- Estado del reporte (habilitado / cuenta regresiva) --------
async function cargarEstado() {
    try {
        const res = await fetch('/api/estado.php');
        const data = await res.json();
        if (!data.ok) return;

        if (!data.usuario_activo) {
            btnAbrirReporte.textContent = 'Ya no formas parte del reto activo';
            btnAbrirReporte.disabled = true;
            return;
        }

        if (data.ya_reporto_hoy) {
            btnAbrirReporte.textContent = '✔️ Ya reportaste hoy';
            btnAbrirReporte.disabled = true;
            clearInterval(countdownInterval);
            return;
        }

        if (data.reporte_habilitado) {
            btnAbrirReporte.textContent = 'Hacer reporte diario';
            btnAbrirReporte.disabled = false;
            clearInterval(countdownInterval);
        } else {
            btnAbrirReporte.disabled = true;
            iniciarCuentaRegresiva(data.segundos_para_reporte);
        }
    } catch (e) {
        btnAbrirReporte.textContent = 'No se pudo cargar el estado';
    }
}

function iniciarCuentaRegresiva(segundosIniciales) {
    let segundos = segundosIniciales;
    clearInterval(countdownInterval);

    const actualizar = () => {
        if (segundos <= 0) {
            clearInterval(countdownInterval);
            cargarEstado();
            return;
        }
        const h = Math.floor(segundos / 3600);
        const m = Math.floor((segundos % 3600) / 60);
        const s = segundos % 60;
        btnAbrirReporte.textContent =
            `Reporte disponible en ${h}h ${m}m ${s}s`;
        segundos--;
    };

    actualizar();
    countdownInterval = setInterval(actualizar, 1000);
}

// -------- Lista de participantes --------
async function cargarParticipantes(orden = 'dias') {
    try {
        const res = await fetch(`/api/usuarios.php?orden=${encodeURIComponent(orden)}`);
        const data = await res.json();
        if (!data.ok) {
            listaParticipantes.innerHTML = '<div class="text-muted-app small">No se pudo cargar la lista.</div>';
            return;
        }

        if (data.usuarios.length === 0) {
            listaParticipantes.innerHTML = '<div class="text-muted-app small">Aún no hay participantes activos.</div>';
            return;
        }

        const medallas = ['🥇', '🥈', '🥉'];

        listaParticipantes.innerHTML = data.usuarios.map((u, i) => {
            const esYo = u.id === window.USUARIO_ACTUAL_ID;
            const inicial = u.nombre.trim().charAt(0).toUpperCase();
            const medalla = orden === 'dias' && medallas[i] ? medallas[i] : '';

            return `
                <a href="perfil.php?id=${u.id}" class="participante ${esYo ? 'es-yo' : ''}" style="text-decoration:none; color:inherit;">
                    <span class="puesto-medalla">${medalla}</span>
                    <span class="avatar-inicial">${inicial}</span>
                    <div>
                        <div class="nombre">${escapeHtml(u.nombre)}${esYo ? ' (tú)' : ''}</div>
                        <div class="meta">${u.rango.icono} ${escapeHtml(u.rango.nombre)}</div>
                    </div>
                    <span class="dias-num">${u.dias}</span>
                </a>
            `;
        }).join('');
    } catch (e) {
        listaParticipantes.innerHTML = '<div class="text-muted-app small">Error de conexión.</div>';
    }
}

function escapeHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto;
    return div.innerHTML;
}

selectorOrden.addEventListener('change', () => cargarParticipantes(selectorOrden.value));

// -------- Modal de reporte --------
btnAbrirReporte.addEventListener('click', () => {
    resultadoSeleccionado = null;
    btnConfirmarReporte.disabled = true;
    errorReporte.classList.add('d-none');
    document.querySelectorAll('.opcion-reporte').forEach(el => {
        el.classList.remove('seleccionada', 'sobrevivi', 'falle');
    });
    modalReporte.show();
});

document.querySelectorAll('.opcion-reporte').forEach(el => {
    el.addEventListener('click', () => {
        document.querySelectorAll('.opcion-reporte').forEach(o => o.classList.remove('seleccionada', 'sobrevivi', 'falle'));
        resultadoSeleccionado = el.dataset.valor;
        el.classList.add('seleccionada', resultadoSeleccionado === 'sobrevivio' ? 'sobrevivi' : 'falle');
        btnConfirmarReporte.disabled = false;
    });
});

btnConfirmarReporte.addEventListener('click', async () => {
    if (!resultadoSeleccionado) return;

    btnConfirmarReporte.disabled = true;
    btnConfirmarReporte.textContent = 'Enviando...';
    errorReporte.classList.add('d-none');

    try {
        const res = await fetch('/api/reportar.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ resultado: resultadoSeleccionado }),
        });
        const data = await res.json();

        if (!res.ok || !data.ok) {
            errorReporte.textContent = data.error || 'No se pudo registrar el reporte';
            errorReporte.classList.remove('d-none');
            btnConfirmarReporte.disabled = false;
            btnConfirmarReporte.textContent = 'Confirmar';
            return;
        }

        modalReporte.hide();
        cargarEstado();
        cargarParticipantes(selectorOrden.value);

        if (data.resultado === 'falle') {
            window.location.reload();
        }
    } catch (e) {
        errorReporte.textContent = 'Error de conexión. Intenta de nuevo.';
        errorReporte.classList.remove('d-none');
        btnConfirmarReporte.disabled = false;
        btnConfirmarReporte.textContent = 'Confirmar';
    } finally {
        btnConfirmarReporte.textContent = 'Confirmar';
    }
});

cargarEstado();
cargarParticipantes();
