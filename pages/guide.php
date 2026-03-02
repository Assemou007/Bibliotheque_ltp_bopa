<?php
// pages/guide.php

logAction($pdo, 'guide', 'vue');
?>

<div class="guide-page">
    <div class="guide-header">
        <h1>📖 Guide d'utilisation</h1>
        <p class="subtitle">Tout ce que vous devez savoir pour utiliser la bibliothèque numérique</p>
    </div>
   
    <div class="guide-toc">
        <h2>Sommaire</h2>
        <ul>
            <li><a href="#introduction">1. Introduction</a></li>
            <li><a href="#navigation">2. Naviguer dans la bibliothèque</a></li>
            <li><a href="#recherche">3. Effectuer une recherche</a></li>
            <li><a href="#documents">4. Consulter et télécharger des documents</a></li>
            <li><a href="#messages">5. Utiliser l'espace public</a></li>
            <li><a href="#chat">6. Utiliser l'assistant virtuel</a></li>
            <li><a href="#astuces">7. Astuces et conseils</a></li>
        </ul>
    </div>
   
    <div class="guide-content">
        <!-- Introduction -->
        <section id="introduction" class="guide-section">
            <h2>1. Introduction</h2>
            <div class="section-content">
                <p>
                    La Bibliothèque Numérique du LTP-BOPA est une plateforme en ligne qui centralise
                    toutes les ressources pédagogiques de l'établissement. Que vous soyez apprenant ou
                    enseignant, vous pouvez accéder à des cours, TD, TP, exercices et bien plus encore,
                    24h/24 et 7j/7.
                </p>
                <div class="tip-box">
                    <strong>💡 Astuce :</strong> Pas besoin de créer un compte ! L'accès est libre et gratuit.
                </div>
            </div>
        </section>
       
        <!-- Navigation -->
        <section id="navigation" class="guide-section">
            <h2>2. Naviguer dans la bibliothèque</h2>
            <div class="section-content">
                <h3>Par filières</h3>
                <p>
                    Depuis la page d'accueil, vous pouvez explorer les documents par filière.
                    Chaque filière est représentée par une carte colorée. Cliquez sur une filière
                    pour voir toutes ses matières.
                </p>
               
                <h3>Par matières</h3>
                <p>
                    Dans chaque filière, les documents sont organisés par matière. Cliquez sur une
                    matière pour voir tous les documents disponibles.
                </p>
               
                <h3>Navigation avancée</h3>
                <p>
                    Utilisez le fil d'Ariane en haut des pages pour vous repérer et revenir facilement
                    en arrière.
                </p>
               
                <div class="screenshot-placeholder">
                    [Capture d'écran - Navigation par filières]
                </div>
            </div>
        </section>
       
        <!-- Recherche -->
        <section id="recherche" class="guide-section">
            <h2>3. Effectuer une recherche</h2>
            <div class="section-content">
                <h3>Recherche simple</h3>
                <p>
                    Utilisez la barre de recherche en haut de chaque page. Tapez des mots-clés
                    (titre du document, nom de la matière, etc.) et appuyez sur Entrée.
                </p>
               
                <h3>Auto-complétion</h3>
                <p>
                    En tapant votre recherche, des suggestions apparaissent automatiquement.
                    Cliquez sur une suggestion pour la sélectionner.
                </p>
               
                <h3>Filtres</h3>
                <p>
                    Sur la page des résultats, vous pouvez filtrer par type de document
                    (cours, TD, TP, exercices, etc.).
                </p>
               
                <div class="tip-box">
                    <strong>💡 Astuce :</strong> Utilisez des mots-clés précis. Par exemple,
                    "algorithmique" plutôt que "cours d'algorithmique".
                </div>
            </div>
        </section>
       
        <!-- Documents -->
        <section id="documents" class="guide-section">
            <h2>4. Consulter et télécharger des documents</h2>
            <div class="section-content">
                <h3>Consultation en ligne</h3>
                <p>
                    Cliquez sur le bouton "Consulter" sur la carte d'un document pour l'ouvrir
                    directement dans votre navigateur. Les PDF s'ouvrent dans un lecteur intégré.
                </p>
               
                <h3>Téléchargement</h3>
                <p>
                    Pour télécharger un document, cliquez sur le bouton "Télécharger". Le fichier
                    sera sauvegardé sur votre appareil pour une consultation hors ligne.
                </p>
               
                <h3>Informations sur les documents</h3>
                <p>
                    Chaque document affiche :
                </p>
                <ul>
                    <li>📌 Son type (cours, TD, TP, etc.)</li>
                    <li>📁 La matière concernée</li>
                    <li>👤 L'auteur (si disponible)</li>
                    <li>📅 La date d'ajout</li>
                    <li>👁️ Le nombre de vues</li>
                </ul>
            </div>
        </section>
       
        <!-- Espace public -->
        <section id="messages" class="guide-section">
            <h2>5. Utiliser l'espace public</h2>
            <div class="section-content">
                <h3>Publier un message</h3>
                <p>
                    Rendez-vous dans l'<a href="index.php?page=messages">Espace public</a> pour
                    partager vos avis, suggestions ou questions. Remplissez le formulaire avec
                    votre nom, le type de message et son contenu.
                </p>
               
                <h3>Modération</h3>
                <p>
                    Les messages sont modérés avant publication pour garantir la qualité des échanges.
                    Ils apparaîtront généralement dans les 24h.
                </p>
               
                <h3>Répondre à un message</h3>
                <p>
                    Vous pouvez répondre aux messages existants en cliquant sur "Répondre"
                    en bas de chaque message.
                </p>
               
                <div class="warning-box">
                    <strong>⚠️ Important :</strong> Restez courtois et respectueux. Les messages
                    injurieux seront rejetés.
                </div>
            </div>
        </section>
       
        <!-- Chat -->
        <section id="chat" class="guide-section">
            <h2>6. Utiliser l'assistant virtuel</h2>
            <div class="section-content">
                <h3>Ouvrir le chat</h3>
                <p>
                    Le chat est disponible en bas à droite de chaque page. Cliquez sur l'en-tête
                    pour l'ouvrir.
                </p>
               
                <h3>Poser une question</h3>
                <p>
                    Tapez votre question dans le champ de texte et appuyez sur Entrée ou cliquez
                    sur le bouton d'envoi. L'assistant vous répondra automatiquement.
                </p>
               
                <h3>Questions suggérées</h3>
                <p>
                    Des suggestions de questions apparaissent au-dessus du champ de texte.
                    Cliquez sur une suggestion pour l'utiliser.
                </p>
               
                <h3>Types de questions</h3>
                <p>
                    L'assistant peut répondre à :
                </p>
                <ul>
                    <li>❓ Questions sur les filières</li>
                    <li>📚 Recherche de documents</li>
                    <li>🔍 Comment utiliser le site</li>
                    <li>ℹ️ Informations générales</li>
                </ul>
            </div>
        </section>
       
        <!-- Astuces -->
        <section id="astuces" class="guide-section">
            <h2>7. Astuces et conseils</h2>
            <div class="section-content">
                <div class="tips-grid">
                    <div class="tip-card">
                        <div class="tip-icon">⏱️</div>
                        <h3>Gagnez du temps</h3>
                        <p>Utilisez les raccourcis clavier : Ctrl+F pour chercher dans une page, Ctrl+clic pour ouvrir un document dans un nouvel onglet.</p>
                    </div>
                   
                    <div class="tip-card">
                        <div class="tip-icon">📱</div>
                        <h3>Version mobile</h3>
                        <p>Le site s'adapte automatiquement à votre écran. Idéal pour consulter sur smartphone ou tablette.</p>
                    </div>
                   
                    <div class="tip-card">
                        <div class="tip-icon">💾</div>
                        <h3>Hors ligne</h3>
                        <p>Téléchargez les documents importants pour les consulter sans connexion internet.</p>
                    </div>
                   
                    <div class="tip-card">
                        <div class="tip-icon">🔔</div>
                        <h3>Nouveautés</h3>
                        <p>Consultez régulièrement la section "Documents récents" pour voir les derniers ajouts.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
   
    <!-- Support -->
    <div class="guide-support">
        <h2>Besoin d'aide supplémentaire ?</h2>
        <div class="support-options">
            <a href="index.php?page=faq" class="support-btn">❓ FAQ</a>
            <a href="index.php?page=contact" class="support-btn">📧 Contact</a>
            <a href="index.php?page=messages" class="support-btn">💬 Espace public</a>
        </div>
    </div>
</div>
