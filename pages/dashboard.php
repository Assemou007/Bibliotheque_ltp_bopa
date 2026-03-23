<?php
// pages/dashboard.php
require_once 'config/database.php';
require_once 'includes/functions.php';
requireAuth();

$user_id = $_SESSION['user_id'];

// Récupérer les infos de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Récupérer les messages de l'utilisateur
$stmt = $pdo->prepare("
    SELECT * FROM messages_publics
    WHERE email = ? OR nom_utilisateur = ?
    ORDER BY created_at DESC
");
$stmt->execute([$user->email, $user->nom]);
$messages = $stmt->fetchAll();

// Statistiques
$stats = [
    'total' => count($messages),
    'approuves' => count(array_filter($messages, fn($m) => $m->statut === 'approuve')),
    'en_attente' => count(array_filter($messages, fn($m) => $m->statut === 'en_attente')),
    'rejetes' => count(array_filter($messages, fn($m) => $m->statut === 'rejete')),
];

// Documents populaires
$popular = $pdo->query("SELECT id, titre FROM documents WHERE est_public = 1 ORDER BY vue_count DESC LIMIT 3")->fetchAll();


?>
    <div class="container">
        <div class="dashboard-container">
            <!-- En-tête avec avatar et bienvenue -->
            <div class="user-header">
                <div class="avatar-circle" style="background-color: <?= getColorFromString($user->nom) ?>;">
                    <?= getInitials($user->nom) ?>
                </div>
                <div>
                    <div class="welcome-text">Bonjour, <?= htmlspecialchars($user->nom) ?> !</div>
                    <p class="welcome-message">Bienvenue dans votre espace personnel. Gérez vos activités et suivez vos contributions.</p>
                </div>
            </div>



            <!-- Deux colonnes : messages récents et actions rapides -->
            <div class="dashboard-grid">
                <!-- Messages récents -->
                <div class="card">
                    <h3>Vos messages récents</h3>
                    <?php if (empty($messages)): ?>
                        <p>Aucun message pour l'instant.</p>
                        <a href="index.php?page=messages#nouveau-message" class="btn-primary" style="margin-top:10px;">Publier un message</a>
                    <?php else: ?>
                        <?php foreach (array_slice($messages, 0, 5) as $msg): ?>
                            <div class="message-item">
                                <div>
                                    <span class="message-type"><?= $msg->type_message ?></span>
                                    <span class="message-title"><?= htmlspecialchars($msg->titre) ?></span>
                                </div>
                                <div>
                                    <span class="message-date"><?= date('d/m/Y', strtotime($msg->created_at)) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="index.php?page=messages" class="btn-primary" style="display:inline-block; margin-top:10px; color:var(--ltp-white);">Voir tous vos messages →</a>
                    <?php endif; ?>
                </div>

                <!-- Actions rapides et documents populaires -->
                <div class="dashboard-section quick-actions">
                    <h3>Actions rapides</h3>
                    <ul class="actions-list">
                        <li><a href="index.php?page=messages#nouveau-message"><i class="fas fa-pen"></i> 💬 Publier un message public</a></li>
                        <li><a href="index.php?page=documents-recents"><i class="fas fa-search"></i> 📚 Voir les nouveaux documents</a></li>
                        <li><a href="index.php?page=profil"><i class="fas fa-user-edit"></i> Modifier mon profil</a></li>
                        <li><a href="index.php?page=deconnexion"><i class="fas fa-user-edit"></i> Deconnexion</a></li>
                    </ul>

                    <h3 style="margin-top: 20px;">📚 Documents populaires</h3>
                    <div class="popular-docs">
                        <?php foreach ($popular as $doc): ?>
                            <div class="popular-doc">
                                <a href="document.php?id=<;?= $doc->id ?>&action=view"><?= htmlspecialchars($doc->titre) ?></a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

