document.addEventListener('DOMContentLoaded', () => {
    const btn  = document.getElementById('modeToggle');
    const body = document.body;

    //  start in stored mode (or light by default)
    if (localStorage.getItem('optimizer-dark') === '1') {
        body.classList.add('dark-mode');
        btn.textContent = '☀️';
    }

    btn.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        const dark = body.classList.contains('dark-mode');
        btn.textContent = dark ? '☀️' : '🌙';
        localStorage.setItem('optimizer-dark', dark ? '1' : '0');
    });
});
