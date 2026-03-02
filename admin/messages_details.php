<?php
// admin/message_detail.php
require_once 'config.php';

$id = (int)($_GET['id'] ?? 0);
$message = $pdo->prepare("SELECT * FROM messages_publics WHERE id = ?")->execute([$id])->fetch();
if (!$message) { header('Location: messages.php'); exit; }

$reponses = $pdo->prepare("SELECT * FROM messages_publics WHERE parent_id = ? ORDER BY created_at")->execute([$id])->fetchAll();

// Traitement de l'ajout d'une réponse
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reponse'])) {
    $contenu = cleanInput($_POST['contenu'] ?? '');
    if (!empty($contenu)) {
        $stmt = $pdo->prepare("INSERT INTO messages_publics (nom_utilisateur, type_message, titre, contenu, parent_id, statut) VALUES ('Modérateur', 'reponse', 'Réponse', ?, ?, 'approuve')");
        $stmt->execute([$contenu, $id]);
        header("Location: message_detail.php?id=id");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Message #<?= $id ?> - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>📚 LTP-BOPA</h2>
                <p>Administration</p>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="index.php" class="active">🏠 Dashboard</a></li>
                    <li><a href="documents.php">📄 Documents</a></li>
                    <li><a href="filieres.php">🏫 Filières</a></li>
                    <li><a href="matieres.php">📘 Matières</a></li>
                    <li><a href="messages.php">💬 Messages publics</a></li>
                    <li><a href="contacts.php">📧 Contacts</a></li>
                    <li><a href="faq.php">❓ FAQ</a></li>
                    <li><a href="statistiques.php">📊 Statistiques</a></li>
                    <li><a href="parametres.php">⚙️ Paramètres</a></li>
                    <li><a href="logout.php">🚪 Déconnexion</a></li>
                </ul>
            </nav>
        </aside>
        <main class="admin-main">
            <h1>Détail du message</h1>
           
            <div class="message-card">
                <div class="message-header">
                    <strong><?= htmlspecialchars($message->nom_utilisateur) ?></strong> - <?= $message->created_at ?>
                </div>
                <h3><?= htmlspecialchars($message->titre) ?></h3>
                <div><?= nl2br(htmlspecialchars($message->contenu)) ?></div>
            </div>
           
            <h2>Réponses</h2>
            <?php foreach ($reponses as $rep): ?>
            <div class="reply-card">
                <strong><?= htmlspecialchars($rep->nom_utilisateur) ?></strong> - <?= $rep->created_at ?><br>
                <?= nl2br(htmlspecialchars($rep->contenu)) ?>
            </div>
            <?php endforeach; ?>
           
            <h3>Ajouter une réponse</h3>
            <form method="POST">
                <textarea name="contenu" rows="4" required style="width:100%"></textarea><br>
                <button type="submit" name="reponse" class="btn-primary">Envoyer</button>
            </form>
           
            <a href="messages.php" class="btn-secondary">← Retour</a>
        </main>
    </div>
</body>
</html>