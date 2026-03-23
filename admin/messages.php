<?php
// admin/messages.php
require_once 'config.php';

// Approuver/rejeter un message
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'approve') {
        $pdo->prepare("UPDATE messages_publics SET statut = 'approuve' WHERE id = ?")->execute([$id]);
    } elseif ($_GET['action'] === 'reject') {
        $pdo->prepare("UPDATE messages_publics SET statut = 'rejete' WHERE id = ?")->execute([$id]);
    }
    header('Location: messages.php');
    exit;
} 

$messages = $pdo->query("
    SELECT * FROM messages_publics
    WHERE parent_id IS NULL
    ORDER BY
        CASE statut
            WHEN 'en_attente' THEN 1
            WHEN 'approuve' THEN 2
            ELSE 3
        END,
        created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Messages publics - Admin</title>
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
            <h1>Messages publics</h1>
           
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Auteur</th>
                        <th>Type</th>
                        <th>Titre</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $msg): ?>
                    <tr class="statut-<?= $msg->statut ?>">
                        <td><?= $msg->id ?></td>
                        <td><?= htmlspecialchars($msg->nom_utilisateur) ?></td>
                        <td><?= $msg->type_message ?></td>
                        <td><?= htmlspecialchars($msg->titre) ?></td>
                        <td>
                            <?php if ($msg->statut == 'en_attente'): ?>
                                <span class="badge warning">En attente</span>
                            <?php elseif ($msg->statut == 'approuve'): ?>
                                <span class="badge success">Approuvé</span>
                            <?php else: ?>
                                <span class="badge error">Rejeté</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($msg->created_at)) ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="message_detail.php?id=<?= $msg->id ?>" class="btn-action btn-view" title="Détail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($msg->statut == 'en_attente'): ?>
                                    <a href="?action=approve&id=<?= $msg->id ?>" class="btn-action btn-approve" onclick="return confirm('Approuver ?')" title="Approuver">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="?action=reject&id=<?= $msg->id ?>" class="btn-action btn-reject" onclick="return confirm('Rejeter ?')" title="Rejeter">
                                        <i class="fas fa-times"></i>
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