<?php
// pages/message-detail.php

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    include 'pages/404.php';
    return;
}

// Récupérer le message principal
$stmt = $pdo->prepare("
    SELECT * FROM messages_publics
    WHERE id = ? AND statut = 'approuve' AND parent_id IS NULL
");
$stmt->execute([$id]);
$message = $stmt->fetch();

if (!$message) {
    include 'pages/404.php';
    return;
}

// Récupérer les réponses
$stmt = $pdo->prepare("
    SELECT * FROM messages_publics
    WHERE parent_id = ? AND statut = 'approuve'
    ORDER BY created_at ASC
");
$stmt->execute([$id]);
$reponses = $stmt->fetchAll();

$type = getMessageTypeLabel($message->type_message);

// Traitement du formulaire de réponse
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'repondre') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error_message = 'Erreur de sécurité.';
    } else {
        $nom = cleanInput($_POST['nom'] ?? '');
        $contenu = cleanInput($_POST['contenu'] ?? '');
       
        if (empty($nom) || empty($contenu)) {
            $error_message = 'Tous les champs sont requis.';
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $stmt = $pdo->prepare("
                INSERT INTO messages_publics (nom_utilisateur, type_message, titre, contenu, parent_id, ip_address, statut)
                VALUES (?, 'reponse', 'Réponse à ' . ?, ?, ?, ?, 'en_attente')
            ");
           
            if ($stmt->execute([$nom, $message->titre, $contenu, $id, $ip])) {
                $success_message = "Votre réponse a été envoyée et sera visible après modération.";
            } else {
                $error_message = "Une erreur est survenue.";
            }
        }
    }
}

logAction($pdo, "message_detail_{$id}", 'vue');
?>

<div class="message-detail-page">
    <div class="message-detail-header">
        <a href="index.php?page=messages" class="back-link">← Retour à l'espace public</a>
    </div>
   
    <!-- Message principal -->
    <div class="message-card detail">
        <div class="message-header">
            <div class="message-type-badge" style="background-color: <?= $type['color'] ?>20; color: <?= $type['color'] ?>">
                <?= $type['icon'] ?> <?= $type['label'] ?>
            </div>
            <div class="message-author-info">
                <span class="message-author"><?= htmlspecialchars($message->nom_utilisateur) ?></span>
                <span class="message-date">📅 <?= date('d/m/Y H:i', strtotime($message->created_at)) ?></span>
            </div>
        </div>
       
        <h1 class="message-title"><?= htmlspecialchars($message->titre) ?></h1>
       
        <div class="message-content">
            <?= nl2br(htmlspecialchars($message->contenu)) ?>
        </div>
       
        <div class="message-footer">
            <button class="btn-reply" onclick="document.getElementById('replyForm').scrollIntoView({behavior: 'smooth'});">
                💬 Répondre à ce message
            </button>
        </div>
    </div>
   
    <!-- Réponses -->
    <?php if (!empty($reponses)): ?>
    <div class="reponses-section">
        <h2>Réponses (<?= count($reponses) ?>)</h2>
       
        <?php foreach ($reponses as $rep): ?>
        <div class="reply-card">
            <div class="reply-header">
                <span class="reply-author"><?= htmlspecialchars($rep->nom_utilisateur) ?></span>
                <span class="reply-date">📅 <?= date('d/m/Y H:i', strtotime($rep->created_at)) ?></span>
            </div>
            <div class="reply-content">
                <?= nl2br(htmlspecialchars($rep->contenu)) ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
   
    <!-- Formulaire de réponse -->
    <div class="reply-form-section" id="replyForm">
        <h2>Ajouter une réponse</h2>
       
        <?php if ($success_message): ?>
            <div class="alert success"><?= $success_message ?></div>
        <?php endif; ?>
       
        <?php if ($error_message): ?>
            <div class="alert error"><?= $error_message ?></div>
        <?php endif; ?>
       
        <form method="POST" class="reply-form">
            <input type="hidden" name="action" value="repondre">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
           
            <div class="form-group">
                <label for="nom">Votre nom ou pseudo *</label>
                <input type="text" id="nom" name="nom" required maxlength="100">
            </div>
           
            <div class="form-group">
                <label for="contenu">Votre réponse *</label>
                <textarea id="contenu" name="contenu" required rows="4"></textarea>
            </div>
           
            <div class="form-actions">
                <button type="submit" class="btn-primary">Envoyer ma réponse</button>
            </div>
           
            <p class="form-note">* Votre réponse sera visible après modération</p>
        </form>
    </div>
</div>
