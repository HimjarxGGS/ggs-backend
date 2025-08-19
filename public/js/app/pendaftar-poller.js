// resources/js/pendaftar-poller.js (minimal, non-destructive)
(function () {
    // console.log("polling initiated");
    const endpoint = window.PENDAFTAR_ENDPOINT || '/admin/pending-registrants-count';
    const POLL_INTERVAL_MS = 5000; // adjust for prod to 10-30s

    async function fetchCount() {
        try {
            const res = await fetch(endpoint, { credentials: 'same-origin', cache: 'no-store' });
            if (!res.ok) throw new Error('Network response not ok: ' + res.status);
            return await res.json();
        } catch (err) {
            console.error('[pendaftar poller] fetch failed', err);
            return null;
        }
    }

    // return array of elements that are likely the Filament badge(s) for the resource nav item
    function findBadgeElements(resourceSlug) {
        const badges = new Set();

        // 1) common Filament badge classes (do not modify classes/styles)
        document.querySelectorAll('.filament-nav-badge, .filament-badge, .badge, .filament-navigation-badge').forEach(el => badges.add(el));

        // 2) find nav links that match resource slug and the numeric child inside them
        if (resourceSlug) {
            const links = Array.from(document.querySelectorAll('a')).filter(a => a.href && a.href.includes(resourceSlug));
            links.forEach(a => {
                // prefer child that only contains a number
                const numericChild = Array.from(a.querySelectorAll('*')).find(ch => {
                    const t = (ch.textContent || '').trim();
                    return t.length > 0 && /^[0-9]+$/.test(t);
                });
                if (numericChild) {
                    badges.add(numericChild);
                } else {
                    // sometimes Filament uses a span as a sibling or last child
                    const possible = a.querySelector('span, i, small');
                    if (possible) badges.add(possible);
                }
            });
        }

        // 3) fallback: any numeric element inside the navigation area (conservative)
        const navRoot = document.querySelector('nav, aside, .filament-navigation');
        if (navRoot) {
            Array.from(navRoot.querySelectorAll('*')).forEach(el => {
                const t = (el.textContent || '').trim();
                if (t.length > 0 && /^[0-9]+$/.test(t) && el.offsetParent !== null) { // visible
                    badges.add(el);
                }
            });
        }

        return Array.from(badges);
    }

    async function tick() {
        console.log('fetching');
        const data = await fetchCount();
        if (!data) return;

        // 🚀 Instead of manipulating DOM directly
        if (window.Livewire) {
            console.log('livewire update');
            window.Livewire.dispatch('pendaftarUpdated', {
                count: data.count,
                color: data.color
            });
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        // initial tick + interval
        tick();
        setInterval(tick, POLL_INTERVAL_MS);
    });
})();
