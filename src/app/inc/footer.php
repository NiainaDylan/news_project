        </div>
    </main>

    <footer class="site-footer">
        <div class="container footer-content">
            <section>
                <h4>A propos</h4>
                <p>ActuFlash propose une selection d informations en continu sur les grands sujets nationaux et internationaux.</p>
            </section>
            <section>
                <h4>Rubriques</h4>
                <p>Politique, Economie, Sport, Culture, Tech</p>
            </section>
            <section>
                <h4>Contact</h4>
                <p>redaction@actuflash.local</p>
                <p>+33 1 00 00 00 00</p>
            </section>
        </div>
        <div class="container footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> ActuFlash. Tous droits reserves.</p>
        </div>
    </footer>
    <script>
        (function () {
            var storageKey = 'actuflash-theme';
            var body = document.body;
            var toggle = document.getElementById('theme-toggle');

            if (!toggle || !body) {
                return;
            }

            function applyTheme(theme) {
                var isDark = theme === 'dark';
                body.classList.toggle('dark-mode', isDark);
                toggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                var nextLabel = isDark ? 'Activer le mode clair' : 'Activer le mode sombre';
                toggle.setAttribute('aria-label', nextLabel);
                toggle.setAttribute('title', nextLabel);
            }

            var savedTheme = localStorage.getItem(storageKey);
            if (savedTheme !== 'dark' && savedTheme !== 'light') {
                savedTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            applyTheme(savedTheme);

            toggle.addEventListener('click', function () {
                var nextTheme = body.classList.contains('dark-mode') ? 'light' : 'dark';
                localStorage.setItem(storageKey, nextTheme);
                applyTheme(nextTheme);
            });
        })();
    </script>
</body>
</html>
