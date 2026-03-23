<?php
// admin/message_detail.php
require_once 'config.php';


$id = (int)($_GET['id'] ?? 0);
 $stmt = $pdo->prepare("SELECT * FROM messages_publics WHERE id = ?");
  $stmt->execute([$id]);
  $message = $stmt->fetch(PDO::FETCH_OBJ);
  if (!$message) { 
    header('Location: messages.php'); 
    exit; 
  }


 $reponses_stmt = $pdo->prepare("SELECT * FROM messages_publics WHERE parent_id = ? ORDER BY created_at ASC");
  $reponses_stmt->execute([$id]);
  $reponses = $reponses_stmt->fetchAll(PDO::FETCH_OBJ);

// Traitement de l'ajout d'une réponse
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reponse'])) {
    $contenu = cleanInput($_POST['contenu'] ?? '');
    if (!empty($contenu)) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $stmt = $pdo->prepare("INSERT INTO messages_publics (nom_utilisateur, email, type_message, titre, contenu, parent_id, ip_address, statut) VALUES ('Modérateur', NULL, 'avis', 'Réponse au message #".$id."', ?, ?, ?, 'approuve')");
        $stmt->execute([$contenu, $id, $ip]);
        $_SESSION['success_message'] = 'Réponse envoyée avec succès !';
        header("Location: message_detail.php?id=$id");
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
<li><a href="index.php" class="active"><i class="fas fa-house"></i> Dashboard</a></li>
                    <li><a href="documents.php"><i class="fas fa-file-pdf"></i> Documents</a></li>
                    <li><a href="filieres.php"><i class="fas fa-school"></i> Filières</a></li>
                    <li><a href="matieres.php"><i class="fas fa-book"></i> Matières</a></li>
                    <li><a href="messages.php"><i class="fas fa-comment"></i> Messages publics</a></li>
                    <li><a href="contacts.php"><i class="fas fa-envelope"></i> Contacts</a></li>
                    <li><a href="faq.php"><i class="fas fa-question-circle"></i> FAQ</a></li>
                    <li><a href="statistiques.php"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
                    <li><a href="parametres.php"><i class="fas fa-cog"></i> Paramètres</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                </ul>
            </nav>
        </aside>
        <main class="admin-main">
            <?php if (isset($_SESSION['success_message'])) { echo '<div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px;">' . htmlspecialchars($_SESSION['success_message']) . '</div>'; unset($_SESSION['success_message']); } ?>
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
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <textarea name="contenu" rows="4" required style="width:100%; box-sizing: border-box; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit;" placeholder="Votre réponse..."></textarea><br><br>
                <button type="submit" name="reponse" class="btn-primary">📤 Envoyer la réponse</button>
            </form>
           
            <a href="messages.php" class="btn-secondary">← Retour</a>
        </main>
    </div>
</body>
</html>