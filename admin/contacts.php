<?php
// admin/contacts.php
require_once 'config.php';

// Marquer comme lu
if (isset($_GET['mark_read']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $pdo->prepare("UPDATE contacts SET lu = 1 WHERE id = ?")->execute([$id]);
    header('Location: contacts.php');
    exit;
}

$contacts = $pdo->query("SELECT * FROM contacts ORDER BY lu ASC, created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Contacts - Admin</title>
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
            <h1>Messages de contact</h1>
           
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Lu</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Sujet</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $c): ?>
                    <tr class="<?= $c->lu ? '' : 'non-lu' ?>">
                        <td><?= $c->lu ? '✅' : '🔴' ?></td>
                        <td><?= htmlspecialchars($c->nom) ?></td>
                        <td><?= htmlspecialchars($c->email) ?></td>
                        <td><?= htmlspecialchars($c->sujet) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($c->created_at)) ?></td>
                        <td>
                            <a href="contact_detail.php?id=<;?= $c->id ?>" class="btn-view">👁️</a>
                            <?php if (!$c->lu): ?>
                                <a href="?mark_read=1&id=<?= $c->id ?>" class="btn-approve" onclick="return confirm('Marquer comme lu ?')">📩</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>