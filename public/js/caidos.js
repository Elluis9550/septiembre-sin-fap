const listaCaidos = document.getElementById('listaCaidos');

async function cargarCaidos() {
    try {
        const res = await fetch('/api/caidos.php');
        const data = await res.json();

        if (!res.ok || !data.ok) {
            listaCaidos.innerHTML = '<div class="text-muted-app small">No se pudo cargar el salón de los caídos.</div>';
            return;
        }

        if (!Array.isArray(data.caidas) || data.caidas.length === 0) {
            listaCaidos.innerHTML = '<div class="text-muted-app small">Todavía no hay caídas registradas.</div>';
            return;
        }

        listaCaidos.innerHTML = data.caidas.map((caida) => {
            const rango = caida.rango || { icono: '💀', nombre: 'Caído' };
            const fecha = new Date(caida.fecha + 'T00:00:00');
            const fechaFormateada = Number.isNaN(fecha.getTime())
                ? caida.fecha
                : fecha.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });

            return `
                <article class="caido-card">
                    <div class="caido-header">
                        <div class="caido-nombre">${escapeHtml(caida.nombre)}</div>
                        <span class="caido-rango">${rango.icono} ${escapeHtml(rango.nombre)}</span>
                    </div>
                    <div class="caido-meta">
                        <span>Días alcanzados: <strong>${caida.dias}</strong></span>
                        <span>Caída: <strong>${fechaFormateada}</strong></span>
                    </div>
                </article>
            `;
        }).join('');
    } catch (error) {
        listaCaidos.innerHTML = '<div class="text-muted-app small">Error al cargar el historial de caídos.</div>';
    }
}

function escapeHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto ?? '';
    return div.innerHTML;
}

cargarCaidos();
