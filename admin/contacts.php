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
                            <div class="action-buttons">
                                <a href="contact_detail.php?id=<?= $c->id ?>" class="btn-action btn-view" title="Détail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if (!$c->lu): ?>
                                    <a href="?mark_read=1&id=<?= $c->id ?>" class="btn-action btn-approve" onclick="return confirm('Marquer lu ?')" title="Marquer lu">
                                        <i class="fas fa-check"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>