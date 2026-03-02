 </div> 
    </main>

    <footer class="main-footer" role="contentinfo">
        <div class="container">
            <div class="footer-grid">
                <!-- À propos -->
                <div class="footer-section">
                    <h3>À propos</h3>
                    <p>Bibliothèque numérique du Lycée Technique Professionnel de Bopa. Ressources pédagogiques pour toutes les filières.</p>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook">📘</a>
                        <a href="#" aria-label="Twitter">🐦</a>
                        <a href="#" aria-label="LinkedIn">🔗</a>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Liens rapides</h3>
                    <ul>
                        <li><a href="index.php?page=a-propos">À propos</a></li>
                        <li><a href="index.php?page=guide">Guide d'utilisation</a></li>
                        <li><a href="index.php?page=faq">FAQ</a></li>
                        <li><a href="index.php?page=contact">Contact</a></li>
                        <li><a href="index.php?page=plan-site">Plan du site</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Filières</h3>
                    <ul>
                        <?php
                        $stmt = $pdo->query("SELECT nom, slug FROM filieres ORDER BY ordre LIMIT 5");
                        while ($filiere = $stmt->fetch()):
                        ?>
                        <li><a href="index.php?page=filiere&slug=<;?= $filiere->slug ?>"><?= $filiere->nom ?></a></li>
                        <?php endwhile; ?>
                        <li><a href="index.php?page=filiere">Toutes les filières</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Contact</h3>
                    <address>
                        <p>📪 <a href="mailto:contact@ltp-bopa.bj">contact@ltp-bopa.bj</a></p>
                        <p>📞 +229 01 23 45 67</p>
                        <p>🗺️ Bopa, Bénin</p>
                <ul class="footer-links">
                    <li><a href="index.php?page=mentions-legales">Mentions légales</a></li>
                    <li><a href="index.php?page=accessibilite">Accessibilité</a></li>
                </ul>
                    </address>
                </div>
            </div>
           
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> LTP-BOPA - Tous droits réservés</p>

            </div>
        </div>
    </footer>
    <?php include 'includes/chat-widget.php'; ?>
    <script src="assets/js/script.js?v=1.0"></script>
</body>
</html>