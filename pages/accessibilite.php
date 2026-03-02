<?php
// pages/accessibilite.php

logAction($pdo, 'accessibilite', 'vue');
?>

<div class="accessibilite-page">
    <div class="access-header">
        <h1>♿ Accessibilité</h1>
        <p class="subtitle">Notre engagement pour un site accessible à tous</p>
    </div>
   
    <div class="access-content">
        <div class="access-section">
            <h2>🌍 Notre engagement</h2>
            <p>
                La bibliothèque numérique du LTP-BOPA s'engage à rendre ses services accessibles
                à tous les utilisateurs, y compris ceux en situation de handicap. Nous travaillons
                continuellement à améliorer l'accessibilité de notre plateforme conformément aux
                normes WCAG 2.1 (Web Content Accessibility Guidelines).
            </p>
        </div>
       
        <div class="access-section">
            <h2>✅ Fonctionnalités d'accessibilité</h2>
           
            <h3>Navigation au clavier</h3>
            <p>
                Le site est entièrement navigable au clavier. Vous pouvez utiliser la touche Tab
                pour vous déplacer entre les éléments interactifs. Un indicateur visuel montre
                l'élément actuellement sélectionné.
            </p>
           
            <h3>Contrastes</h3>
            <p>
                Tous les textes respectent un ratio de contraste minimum de 4.5:1 par rapport à
                leur arrière-plan, conformément aux recommandations WCAG AA.
            </p>
           
            <h3>Redimensionnement du texte</h3>
            <p>
                Vous pouvez agrandir la taille du texte jusqu'à 200% sans perte de fonctionnalité
                ou de lisibilité. Utilisez les commandes de zoom de votre navigateur (Ctrl + ou Cmd +).
            </p>
           
            <h3>Structure sémantique</h3>
            <p>
                Le site utilise des balises HTML sémantiques (header, nav, main, article, footer)
                pour une meilleure compréhension par les lecteurs d'écran.
            </p>
        </div>
       
        <div class="access-section">
            <h2>📱 Navigation simplifiée</h2>
            <ul>
                <li>✓ "Skip to content" - Lien pour passer directement au contenu principal</li>
                <li>✓ Fil d'Ariane pour se repérer dans la navigation</li>
                <li>✓ Titres de pages clairs et descriptifs</li>
                <li>✓ Alternatives textuelles pour les images (attributs alt)</li>
                <li>✓ Formulaires avec labels associés</li>
            </ul>
        </div>
       
        <div class="access-section">
            <h2>🔧 Personnalisation</h2>
           
            <div class="access-tools">
                <h3>Outils d'accessibilité</h3>
                <button class="access-tool" onclick="toggleHighContrast()">Activer le haut contraste</button>
                <button class="access-tool" onclick="increaseFontSize()">Agrandir le texte</button>
                <button class="access-tool" onclick="decreaseFontSize()">Réduire le texte</button>
                <button class="access-tool" onclick="resetAccessibility()">Réinitialiser</button>
            </div>
        </div>
       
        <div class="access-section">
            <h2>📞 Nous aider à améliorer l'accessibilité</h2>
            <p>
                Si vous rencontrez des difficultés d'accès à certains contenus ou fonctionnalités,
                n'hésitez pas à nous en faire part. Nous sommes à votre écoute pour améliorer
                constamment l'expérience de tous les utilisateurs.
            </p>
           
            <div class="access-contact">
                <p><strong>Email :</strong> <a href="mailto:accessibilite@ltp-bopa.bj">accessibilite@ltp-bopa.bj</a></p>
                <p><strong>Téléphone :</strong> +229 01 23 45 67</p>
                <p><strong>Formulaire :</strong> <a href="index.php?page=contact">Page de contact</a></p>
            </div>
        </div>
       
        <div class="access-section">
            <h2>📋 Déclaration d'accessibilité</h2>
            <p>
                Cette déclaration a été établie le 1er mars 2026. Nous nous engageons à mettre à jour
                cette déclaration régulièrement pour refléter nos progrès en matière d'accessibilité.
            </p>
            <p>
                <strong>Statut de conformité :</strong> Partiellement conforme (en cours d'amélioration)
            </p>
            <p>
                <strong>Dernière mise à jour :</strong> Mars 2026
            </p>
        </div>
    </div>
</div>

<script>
let currentFontSize = 100;

function toggleHighContrast() {
    document.body.classList.toggle('high-contrast');
}

function increaseFontSize() {
    if (currentFontSize < 150) {
        currentFontSize += 10;
        document.documentElement.style.fontSize = currentFontSize + '%';
    }
}

function decreaseFontSize() {
    if (currentFontSize > 70) {
        currentFontSize -= 10;
        document.documentElement.style.fontSize = currentFontSize + '%';
    }
}

function resetAccessibility() {
    document.body.classList.remove('high-contrast');
    currentFontSize = 100;
    document.documentElement.style.fontSize = '100%';
}
</script>
