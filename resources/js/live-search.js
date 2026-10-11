const liveSearchRequests = new WeakMap();

function liveSearchUrl(form) {
    const url = new URL(form.action, window.location.origin);
    const params = new URLSearchParams(new FormData(form));

    for (const [key, value] of [...params.entries()]) {
        if (value.trim() === '') {
            params.delete(key);
        }
    }

    params.delete('page');
    url.search = params.toString();

    return url;
}

async function updateLiveSearch(form, url = liveSearchUrl(form), updateHistory = true) {
    liveSearchRequests.get(form)?.abort();

    const controller = new AbortController();
    liveSearchRequests.set(form, controller);
    form.setAttribute('aria-busy', 'true');

    const feedback = form.querySelector('[data-live-feedback]');
    if (feedback) feedback.textContent = '';

    try {
        const response = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            signal: controller.signal,
        });

        if (!response.ok) {
            throw new Error(`Pencarian gagal dengan status ${response.status}.`);
        }

        const html = await response.text();
        const parsed = new DOMParser().parseFromString(html, 'text/html');
        const resultRows = parsed.querySelector(form.dataset.liveTarget);
        const currentRows = document.querySelector(form.dataset.liveTarget);
        const resultCount = parsed.querySelector(form.dataset.liveCount);
        const currentCount = document.querySelector(form.dataset.liveCount);
        const resultPagination = parsed.querySelector(form.dataset.livePagination);
        const currentPagination = document.querySelector(form.dataset.livePagination);

        if (!resultRows || !currentRows || !resultCount || !currentCount || !resultPagination || !currentPagination) {
            throw new Error('Struktur hasil pencarian tidak ditemukan.');
        }

        currentRows.innerHTML = resultRows.innerHTML;
        currentCount.textContent = resultCount.textContent;
        currentPagination.innerHTML = resultPagination.innerHTML;

        if (updateHistory) {
            window.history.replaceState({}, '', url);
        }
    } catch (error) {
        if (error.name !== 'AbortError') {
            console.error(error);
            if (feedback) feedback.textContent = 'Pencarian gagal. Silakan coba lagi.';
        }
    } finally {
        if (liveSearchRequests.get(form) === controller) {
            form.removeAttribute('aria-busy');
        }
    }
}

document.querySelectorAll('form[data-live-search]').forEach((form) => {
    const searchInput = form.querySelector('input[type="search"]');
    let debounceTimer;

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        clearTimeout(debounceTimer);
        updateLiveSearch(form);
    });

    searchInput?.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(() => updateLiveSearch(form), 250);
    });

    form.querySelectorAll('select').forEach((select) => {
        select.addEventListener('change', () => {
            clearTimeout(debounceTimer);
            updateLiveSearch(form);
        });
    });
});
