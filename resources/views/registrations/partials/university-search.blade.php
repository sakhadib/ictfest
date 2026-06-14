<script>
(() => {
    const universities = @json($universities ?? []);

    if (!Array.isArray(universities) || universities.length === 0) {
        return;
    }

    const normalize = (value) => String(value ?? '')
        .normalize('NFKD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, ' ')
        .trim();

    const tokenize = (value) => normalize(value).split(' ').filter(Boolean);

    const scoreEntry = (entry, query) => {
        const name = normalize(entry.university_name);
        const acronym = normalize(entry.acronym);

        if (!query) {
            return 1;
        }

        let score = 0;

        if (name === query) score += 1000;
        if (acronym === query) score += 950;
        if (name.startsWith(query)) score += 700;
        if (acronym.startsWith(query)) score += 650;
        if (name.includes(query)) score += 500;
        if (acronym.includes(query)) score += 450;

        const tokens = tokenize(query);
        if (tokens.length) {
            const everyTokenMatches = tokens.every((token) => name.includes(token) || acronym.includes(token));

            if (everyTokenMatches) {
                score += 260;
            }

            const firstToken = tokens[0];
            if (firstToken && name.includes(firstToken)) {
                score += 80;
            }

            if (tokens.length > 1) {
                const initials = tokens.map((token) => token[0]).join('');
                if (acronym.startsWith(initials)) {
                    score += 150;
                }
            }
        }

        score -= name.length / 100;

        return score > 0 ? score : 0;
    };

    const makeSuggestion = (entry) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'flex w-full items-start justify-between gap-4 border-b border-white/8 px-4 py-3 text-left text-sm text-white/80 transition last:border-b-0 hover:bg-white/8';

        const meta = [];

        if (entry.acronym) meta.push(entry.acronym);
        if (entry.location) meta.push(entry.location);
        if (entry.type) meta.push(entry.type);

        const content = document.createElement('span');
        content.className = 'min-w-0';

        const title = document.createElement('span');
        title.className = 'block font-medium text-white';
        title.textContent = entry.university_name;
        content.appendChild(title);

        const metaLine = document.createElement('span');
        metaLine.className = 'mt-1 block text-xs text-white/42';
        metaLine.textContent = meta.join(' • ');
        content.appendChild(metaLine);

        const badge = document.createElement('span');
        badge.className = 'shrink-0 rounded-full border border-white/10 bg-white/[.04] px-2 py-1 text-[10px] uppercase tracking-[.16em] text-white/42';
        badge.textContent = 'Select';

        button.appendChild(content);
        button.appendChild(badge);

        return button;
    };

    const attachSearch = (input) => {
        if (input.dataset.universitySearchReady === 'true') {
            return;
        }

        input.dataset.universitySearchReady = 'true';
        input.autocomplete = 'off';
        input.spellcheck = false;

        const host = input.parentElement;

        if (!host) {
            return;
        }

        host.style.position = host.style.position || 'relative';

        const panel = document.createElement('div');
        panel.className = 'absolute left-0 right-0 top-full z-30 mt-2 hidden overflow-hidden rounded-md border border-white/10 bg-ink shadow-[0_24px_60px_rgba(0,0,0,.35)]';
        host.appendChild(panel);

        let hideTimer = null;

        const render = () => {
            const query = normalize(input.value);
            const results = universities
                .map((entry) => ({ entry, score: scoreEntry(entry, query) }))
                .filter((item) => !query || item.score > 0)
                .sort((left, right) => right.score - left.score || left.entry.university_name.localeCompare(right.entry.university_name))
                .slice(0, 8)
                .map((item) => item.entry);

            panel.innerHTML = '';

            if (results.length === 0) {
                panel.classList.add('hidden');
                return;
            }

            results.forEach((entry) => {
                const option = makeSuggestion(entry);

                option.addEventListener('pointerdown', (event) => {
                    event.preventDefault();
                    input.value = entry.university_name;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    panel.classList.add('hidden');
                });

                panel.appendChild(option);
            });

            panel.classList.remove('hidden');
        };

        const scheduleHide = () => {
            clearTimeout(hideTimer);
            hideTimer = window.setTimeout(() => panel.classList.add('hidden'), 120);
        };

        input.addEventListener('focus', render);
        input.addEventListener('input', render);
        input.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowDown' && !panel.classList.contains('hidden')) {
                event.preventDefault();
                panel.querySelector('button')?.focus();
            }

            if (event.key === 'Escape') {
                panel.classList.add('hidden');
            }
        });
        input.addEventListener('blur', scheduleHide);
        input.addEventListener('click', render);

        panel.addEventListener('pointerdown', (event) => {
            event.preventDefault();
        });

        document.addEventListener('click', (event) => {
            if (event.target === input || panel.contains(event.target)) {
                return;
            }

            panel.classList.add('hidden');
        });
    };

    document.querySelectorAll('[data-university-search]').forEach(attachSearch);
})();
</script>
