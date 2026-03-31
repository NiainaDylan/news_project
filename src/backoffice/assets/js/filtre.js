(function () {
            const form = document.getElementById('article-filters');
            const resetBtn = document.getElementById('filters-reset');
            const feedback = document.getElementById('filters-feedback');
            const container = document.getElementById('articles-container');
            const limitInput = document.getElementById('filter-limit');

            function escapeHtml(value) {
                return String(value)
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function extractImageSrc(html) {
                const content = String(html || '');
                const wrapper = document.createElement('div');
                wrapper.innerHTML = content;
                const img = wrapper.querySelector('img');
                if (!img) {
                    return '';
                }

                const src = (img.getAttribute('src') || '').trim();
                if (!src) {
                    return '';
                }

                if (/^https?:\/\//i.test(src) || src.startsWith('data:image/')) {
                    return src;
                }

                if (src.startsWith('/var/www/html/')) {
                    return '/' + src.slice('/var/www/html/'.length).replace(/^\/+/, '');
                }

                if (src.startsWith('./')) {
                    return src.slice(1);
                }

                if (src.startsWith('../')) {
                    return '/' + src.replace(/^(\.\.\/)+/, '').replace(/^\/+/, '');
                }

                if (src.startsWith('/')) {
                    return src;
                }

                if (src.startsWith('uploads/')) {
                    return '/' + src;
                }

                return '';
            }

            function extractImageAlt(html) {
                const content = String(html || '');
                const wrapper = document.createElement('div');
                wrapper.innerHTML = content;
                const img = wrapper.querySelector('img');
                if (!img) {
                    return 'Image article';
                }

                const alt = (img.getAttribute('alt') || '').trim();
                return alt || 'Image article';
            }

            function excerptText(html, limit = 220) {
                const content = String(html || '');
                const wrapper = document.createElement('div');
                wrapper.innerHTML = content;
                const text = (wrapper.textContent || wrapper.innerText || '').replace(/\s+/g, ' ').trim();

                if (!text) {
                    return 'Aucun contenu texte';
                }

                return text.length > limit ? `${text.slice(0, limit - 3)}...` : text;
            }

            function renderArticles(articles) {
                if (!Array.isArray(articles) || articles.length === 0) {
                    container.innerHTML = '<p>Aucun article trouvé.</p>';
                    return;
                }

                const cards = articles.map((article) => {
                    const id = Number(article.id || 0);
                    const categorie = article.categorie ? `<span class="article-card__badge">${escapeHtml(article.categorie)}</span>` : '';
                    const date = article.date_ ? `<span class="article-card__date">${escapeHtml(article.date_)}</span>` : '';
                    const source = article.source ? `<div class="article-card__source">Source : ${escapeHtml(article.source)}</div>` : '';
                    const raw = article.valeur ? String(article.valeur) : '';
                    const imageSrc = extractImageSrc(raw);
                    const imageAlt = extractImageAlt(raw);
                    const imageHtml = imageSrc
                        ? `<div class="article-card__thumb-wrap"><img class="article-card__thumb" src="${escapeHtml(imageSrc)}" alt="${escapeHtml(imageAlt)}"></div>`
                        : '';
                    const content = excerptText(raw);
                    const editUrl = `/backoffice/?action=article_add&id=${id}`;
                    const detailUrl = `/backoffice/?action=article_detail&id=${id}`;

                    return `<div class="article-card">
                                <div class="article-card__meta">
                                    <span class="article-card__id">#${id}</span>
                                    ${categorie}
                                    ${date}
                                </div>
                                ${imageHtml}
                                <div class="article-card__content">${escapeHtml(content)}</div>
                                ${source}
                                <div class="article-card__actions">
                                    <a class="btn btn-secondary" href="${editUrl}">Éditer</a>
                                    <a class="btn" href="${detailUrl}">Voir details</a>
                                </div>
                            </div>`;
                }).join('');

                container.innerHTML = `<div class="articles-grid">${cards}</div>`;
            }

            async function submitFilters() {
                feedback.textContent = 'Chargement...';
                feedback.classList.remove('is-error');

                const formData = new FormData(form);
                if (!formData.get('limit_insertion')) {
                    formData.set('limit_insertion', '10');
                }

                try {
                    const response = await fetch('/backoffice/?action=article_filter', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    const contentType = response.headers.get('content-type') ?? '';
                    let result;
                    if (contentType.includes('application/json')) {
                        result = await response.json();
                    } else {
                        const rawText = await response.text();
                        throw new Error(`Réponse invalide du serveur (${response.status})`);
                    }

                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Erreur lors du filtrage');
                    }

                    renderArticles(result.articles || []);
                    feedback.textContent = `${result.count ?? 0} article(s) affiché(s) - période: mois actuel`;
                } catch (error) {
                    feedback.textContent = error instanceof Error ? error.message : 'Erreur inattendue';
                    feedback.classList.add('is-error');
                }
            }

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                submitFilters();
            });

            resetBtn.addEventListener('click', function () {
                form.reset();
                const firstCategory = form.querySelector('input[name="id_categorie"][value=""]');
                if (firstCategory) {
                    firstCategory.checked = true;
                }

                const firstStatus = form.querySelector('input[name="statut"][value=""]');
                if (firstStatus) {
                    firstStatus.checked = true;
                }

                const sourceSelect = document.getElementById('filter-source');
                if (sourceSelect) {
                    sourceSelect.value = '';
                }

                const periodSelect = document.getElementById('filter-period');
                if (periodSelect) {
                    periodSelect.value = 'mois_courant_auto';
                }

                if (limitInput) {
                    limitInput.value = '10';
                }

                submitFilters();
            });
        })();