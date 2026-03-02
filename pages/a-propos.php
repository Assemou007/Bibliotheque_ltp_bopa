<?php
// pages/a-propos.php

// Statistiques générales
$stats = [
    'documents' => $pdo->query("SELECT COUNT(*) as count FROM documents WHERE est_public = 1")->fetch()->count,
    'filieres' => $pdo->query("SELECT COUNT(*) as count FROM filieres")->fetch()->count,
    'matieres' => $pdo->query("SELECT COUNT(*) as count FROM matieres")->fetch()->count,
    'messages' => $pdo->query("SELECT COUNT(*) as count FROM messages_publics WHERE statut = 'approuve'")->fetch()->count,
    'utilisateurs_actifs' => $pdo->query("SELECT COUNT(DISTINCT session_id) as count FROM chat_history WHERE created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch()->count
];

// Derniers ajouts
$derniers_docs = $pdo->query("
    SELECT d.titre, d.created_at, f.nom as filiere_nom
    FROM documents d
    JOIN matieres m ON d.matiere_id = m.id
    JOIN filieres f ON m.filiere_id = f.id
    WHERE d.est_public = 1
    ORDER BY d.created_at DESC
    LIMIT 5
")->fetchAll();

logAction($pdo, 'a-propos', 'vue');
?>

<div class="about-page">
    <div class="about-header">
        <h1>À propos de la Bibliothèque Numérique</h1>
        <p class="subtitle">LTP-BOPA - Lycée Technique Professionnel de Bopa</p>
    </div>
   
    <div class="about-content">
        <div class="about-section">
            <h2>📖 Notre mission</h2>
            <p>
                La Bibliothèque Numérique du LTP-BOPA a été créée dans le but de faciliter l'accès aux ressources pédagogiques
                pour tous les apprenants et enseignants de l'établissement. Notre mission est de centraliser, organiser et
                rendre accessible l'ensemble des documents nécessaires à la formation professionnelle.
            </p>
        </div>
       
        <div class="about-section">
            <h2>🎯 Objectifs</h2>
            <ul class="objectives-list">
                <li>✓ Offrir un accès permanent aux ressources pédagogiques, 24h/24 et 7j/7</li>
                <li>✓ Organiser les contenus par filière et matière pour faciliter la recherche</li>
                <li>✓ Moderniser l'apprentissage avec des outils numériques</li>
                <li>✓ Permettre aux apprenants de progresser à leur rythme</li>
                <li>✓ Favoriser l'autonomie dans la recherche d'information</li>
            </ul>
        </div>
       
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-number"><?= $stats['documents'] ?></div>
                <div class="stat-label">Documents</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🏫</div>
                <div class="stat-number"><?= $stats['filieres'] ?></div>
                <div class="stat-label">Filières</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📘</div>
                <div class="stat-number"><?= $stats['matieres'] ?></div>
                <div class="stat-label">Matières</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💬</div>
                <div class="stat-number"><?= $stats['messages'] ?></div>
                <div class="stat-label">Messages</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-number"><?= $stats['utilisateurs_actifs'] ?></div>
                <div class="stat-label">Visiteurs/semaine</div>
            </div>
        </div>
       
        <!-- Équipe -->
        <div class="about-section">
            <h2>👥 Équipe pédagogique</h2>
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-avatar">👨‍🏫</div>
                    <h3>Commission pédagogique</h3>
                    <p>Direction des études</p>
                </div>
                <div class="team-member">
                    <div class="member-avatar">👩‍💻</div>
                    <h3>Développement</h3>
                    <p>Apprenants LTP-BOPA</p>
                </div>
                <div class="team-member">
                    <div class="member-avatar">📋</div>
                    <h3>Modération</h3>
                    <p>Équipe enseignante</p>
                </div>
            </div>
        </div>
       
        <!-- Derniers ajouts -->
        <div class="about-section">
            <h2>🆕 Derniers ajouts</h2>
            <div class="recent-additions">
                <?php foreach ($derniers_docs as $doc): ?>
                <div class="recent-item">
                    <span class="recent-icon">📄</span>
                    <span class="recent-title"><?= htmlspecialchars($doc->titre) ?></span>
                    <span class="recent-meta"><?= $doc->filiere_nom ?></span>
                    <span class="recent-date"><?= date('d/m/Y', strtotime($doc->created_at)) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
       
        <!-- Contact rapide -->
        <div class="about-section contact-rapide">
            <h2>📞 Contactez-nous</h2>
            <div class="contact-info">
                <p><strong>Adresse :</strong> LTP-BOPA, Bopa, Bénin</p>
                <p><strong>Téléphone :</strong> +229 01 23 45 67</p>
                <p><strong>Email :</strong> <a href="mailto:bibliotheque@ltp-bopa.bj">bibliotheque@ltp-bopa.bj</a></p>
                <p><strong>Horaires :</strong> Service disponible 24h/24 en ligne</p>
            </div>
            <a href="index.php?page=contact" class="btn-contact">Formulaire de contact →</a>
        </div>
    </div>
</div>
