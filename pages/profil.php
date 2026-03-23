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
?>
<div class="user-header">
        <div class="avatar-circle" style="background-color: <?= getColorFromString($user->nom) ?>;">
        <?= getInitials($user->nom) ?>
</div>
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