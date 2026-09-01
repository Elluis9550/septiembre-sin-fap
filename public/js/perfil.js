async function cargarGrid() {
    const grid = document.getElementById('gridContribuciones');
    try {
        const res = await fetch(`/api/reportes.php?id=${window.PERFIL_USUARIO_ID}`);
        const data = await res.json();

        if (!data.ok) {
            grid.innerHTML = '<div class="text-muted-app small">No se pudo cargar el calendario.</div>';
            return;
        }

        grid.innerHTML = data.dias.map(d => {
            const clase = d.estado === 'sin_reporte' ? '' : d.estado;
            const numeroDia = parseInt(d.fecha.split('-')[2], 10);
            return `<div class="celda-dia ${clase}" title="${d.fecha}">
                        <span style="font-size:0.55rem; opacity:0.5; display:block; text-align:center; padding-top:2px;">${numeroDia}</span>
                    </div>`;
        }).join('');
    } catch (e) {
        grid.innerHTML = '<div class="text-muted-app small">Error de conexión.</div>';
    }
}

cargarGrid();
