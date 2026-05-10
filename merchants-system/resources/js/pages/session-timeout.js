document.addEventListener('DOMContentLoaded', function () {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const logoutForm = document.getElementById('manual-logout-form');

    if (!csrfMeta || !logoutForm) return;

    const IDLE_LIMIT_MINUTES = 15;
    const IDLE_LIMIT_MS = IDLE_LIMIT_MINUTES * 60 * 1000;

    const csrfToken = csrfMeta.getAttribute('content');
    const logoutUrl = logoutForm.getAttribute('action');
    const loginUrl = '/login';

    let idleTimer = null;
    let isLoggingOut = false;

    function doTimeoutLogout() {
        if (isLoggingOut) return;
        isLoggingOut = true;

        const formData = new URLSearchParams();
        formData.append('_token', csrfToken);
        formData.append('ended_by', 'timeout');

        fetch(logoutUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json, text/html'
            },
            body: formData.toString(),
            credentials: 'same-origin'
        }).finally(() => {
            window.location.href = loginUrl;
        });
    }

    function resetIdleTimer() {
        if (isLoggingOut) return;
        clearTimeout(idleTimer);
        idleTimer = setTimeout(doTimeoutLogout, IDLE_LIMIT_MS);
    }

    ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'click'].forEach((eventName) => {
        document.addEventListener(eventName, resetIdleTimer, { passive: true });
    });

    resetIdleTimer();
});
