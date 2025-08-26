let waitInterval = null;
let waitStart = null;

function formatElapsed(ms) {
    const s = Math.floor(ms / 1000);
    const mins = String(Math.floor(s / 60)).padStart(2, '0');
    const secs = String(s % 60).padStart(2, '0');
    return `${mins}:${secs}`;
}

function startWait(message) {
    const overlay = document.getElementById('waitOverlay');
    if (!overlay) return;
    document.getElementById('waitMessage').textContent = message || 'Procesando...';
    overlay.style.display = 'flex';
    waitStart = Date.now();
    document.getElementById('waitTimer').textContent = '00:00';
    waitInterval = setInterval(() => {
        document.getElementById('waitTimer').textContent = formatElapsed(Date.now() - waitStart);
    }, 250);
    // optionally disable form buttons:
    document.querySelectorAll('button, input[type="submit"]').forEach(el => el.disabled = true);
}

function stopWait() {
    const overlay = document.getElementById('waitOverlay');
    if (!overlay) return;
    overlay.style.display = 'none';
    clearInterval(waitInterval);
    waitInterval = null;
    waitStart = null;
    document.querySelectorAll('button, input[type="submit"]').forEach(el => el.disabled = false);
}