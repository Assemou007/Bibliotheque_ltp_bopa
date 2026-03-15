<?php
// pages/dashboard.php
require_once 'config/database.php';
require_once 'includes/functions.php';
requireAuth(); // L'utilisateur doit être connecté

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Récupérer les messages de l'utilisateur
$messages = $pdo->prepare("
    SELECT * FROM messages_publics
    WHERE email = ? OR nom_utilisateur = ?
    ORDER BY created_at DESC
");
$messages->execute([$user->email, $user->nom]);
$messages = $messages->fetchAll();

// Compter les messages par statut
$stats = [
    'total' => count($messages),
    'approuves' => count(array_filter($messages, fn($m) => $m->statut === 'approuve')),
    'en_attente' => count(array_filter($messages, fn($m) => $m->statut === 'en_attente')),
    'rejetes' => count(array_filter($messages, fn($m) => $m->statut === 'rejete')),
];

// Derniers documents consultés (à implémenter si tu veux, sinon on peut afficher des suggestions)
$derniers_docs = $pdo->query("
    SELECT d.titre, d.id, m.nom as matiere_nom
    FROM documents d
    JOIN matieres m ON d.matiere_id = m.id
    WHERE d.est_public = 1
    ORDER BY d.created_at DESC
    LIMIT 5
")->fetchAll();
?>
<div class="dashboard-page">
    <div class="dashboard-header">
        <h1>Bonjour, <?= htmlspecialchars($user->nom) ?> !</h1>
        <p class="welcome-text">Bienvenue dans votre espace personnel. Gérez vos activités et suivez vos contributions.</p>
    </div>

    <!-- Cartes de statistiques -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <span class="stat-icon">📝</span>
            <div class="stat-detail">
                <span class="stat-value"><?= $stats['total'] ?></span>
                <span class="stat-label">Messages publiés</span>
            </div>
        </div>
        <div class="stat-card success">
            <span class="stat-icon">✅</span>
            <div class="stat-detail">
                <span class="stat-value"><?= $stats['approuves'] ?></span>
                <span class="stat-label">Approuvés</span>
            </div>
        </div>
        <div class="stat-card warning">
            <span class="stat-icon">⏳</span>
            <div class="stat-detail">
                <span class="stat-value"><?= $stats['en_attente'] ?></span>
                <span class="stat-label">En attente</span>
            </div>
        </div>
        <div class="stat-card danger">
            <span class="stat-icon">❌</span>
            <div class="stat-detail">
                <span class="stat-value"><?= $stats['rejetes'] ?></span>
                <span class="stat-label">Rejetés</span>
            </div>
        </div>
    </div>

    <!-- Deux colonnes : Messages récents et actions rapides -->
    <div class="dashboard-grid">
        <!-- Colonne de gauche : Messages récents -->
        <section class="dashboard-section recent-messages">
            <h2>📬 Vos messages récents</h2>
            <?php if (empty($messages)): ?>
                <p class="no-data">Vous n'avez encore publié aucun message.</p>
                <a href="index.php?page=messages#nouveau-message" class="btn-primary">Publier un message</a>
            <?php else: ?>
                <div class="messages-list">
                    <?php foreach (array_slice($messages, 0, 5) as $msg): ?>
                        <div class="message-item">
                            <span class="message-type <?= $msg->type_message ?>"><?= getMessageTypeLabel($msg->type_message)['icon'] ?> <?= getMessageTypeLabel($msg->type_message)['label'] ?></span>
                            <a href="index.php?page=message-detail&id=<;?= $msg->id ?>" class="message-title"><?= htmlspecialchars($msg->titre) ?></a>
                            <span class="message-status status-<?= $msg->statut ?>">
                                <?php if ($msg->statut === 'approuve'): ?>✅ Approuvé
                                <?php elseif ($msg->statut === 'en_attente'): ?>⏳ En attente
                                <?php else: ?>❌ Rejeté
                                <?php endif; ?>
                            </span>
                            <span class="message-date"><?= date('d/m/Y', strtotime($msg->created_at)) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($messages) > 5): ?>
                    <a href="index.php?page=utilisateur-messages" class="btn-link">Voir tous vos messages →</a>
                <?php endif; ?>
            <?php endif; ?>
        </section>

        <!-- Colonne de droite : Actions rapides et suggestions -->
        <section class="dashboard-section quick-actions">
            <h2>⚡ Actions rapides</h2>
            <ul class="actions-list">
                <li><a href="index.php?page=messages#nouveau-message">📢 Publier un message public</a></li>
                <li><a href="index.php?page=documents-recents">📚 Voir les nouveaux documents</a></li>
                <li><a href="index.php?page=profil">👤 Modifier mon profil</a></li>
                <li><a href="index.php?page=parametres-compte">⚙️ Paramètres du compte</a></li>
                <li><a href="index.php?page=deconnexion">🚪 Déconnexion</a></li>
            </ul>

            <h2 style="margin-top: 2rem;">🔥 Documents populaires</h2>
            <div class="popular-docs">
                <?php foreach ($derniers_docs as $doc): ?>
                    <div class="popular-doc">
                        <a href="document.php?id=<;?= $doc->id ?>&action=view"><?= htmlspecialchars($doc->titre) ?></a>
                        <span><?= htmlspecialchars($doc->matiere_nom) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</div>