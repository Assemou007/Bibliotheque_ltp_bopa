<?php
// admin/contact_detail.php
require_once 'config.php';

$id = (int)($_GET['id'] ?? 0);
$contact = $pdo->prepare("SELECT * FROM contacts WHERE id = ?")->execute([$id])->fetch();
if (!$contact) { header('Location: contacts.php'); exit; }

// Marquer comme lu si pas déjà fait
if (!$contact->lu) {
    $pdo->prepare("UPDATE contacts SET lu = 1 WHERE id = ?")->execute([$id]);
    $contact->lu = 1;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Contact #<?= $id ?> - Admin</title>
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
            <h1>Détail du contact</h1>
           
            <div class="contact-detail">
                <p><strong>Nom :</strong> <?= htmlspecialchars($contact->nom) ?></p>
                <p><strong>Email :</strong> <a href="mailto:<?= $contact->email ?>"><?= htmlspecialchars($contact->email) ?></a></p>
                <p><strong>Sujet :</strong> <?= htmlspecialchars($contact->sujet) ?></p>
                <p><strong>Date :</strong> <?= $contact->created_at ?></p>
                <p><strong>IP :</strong> <?= $contact->ip_address ?? 'N/A' ?></p>
                <p><strong>Message :</strong></p>
                <div class="message-content"><?= nl2br(htmlspecialchars($contact->message)) ?></div>
            </div>
           
            <div class="actions">
                <a href="mailto:<?= $contact->email ?>?subject=RE: <?= urlencode($contact->sujet) ?>" class="btn-primary">📧 Répondre par email</a>
                <a href="contacts.php" class="btn-secondary">← Retour</a>
            </div>
        </main>
    </div>
</body>
</html>