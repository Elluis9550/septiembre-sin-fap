document.getElementById('formLogin').addEventListener('submit', async (e) => {
    e.preventDefault();

    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    const btn = document.getElementById('btnSubmit');
    const errorBox = document.getElementById('mensajeError');

    errorBox.classList.add('d-none');
    btn.disabled = true;
    btn.textContent = 'Entrando...';

    try {
        const res = await fetch('/api/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password }),
        });
        const data = await res.json();

        if (!res.ok || !data.ok) {
            errorBox.textContent = data.error || 'No se pudo iniciar sesión';
            errorBox.classList.remove('d-none');
            btn.disabled = false;
            btn.textContent = 'Entrar al reto';
            return;
        }

        window.location.href = '/dashboard.php';
    } catch (err) {
        errorBox.textContent = 'Error de conexión. Intenta de nuevo.';
        errorBox.classList.remove('d-none');
        btn.disabled = false;
        btn.textContent = 'Entrar al reto';
    }
});
