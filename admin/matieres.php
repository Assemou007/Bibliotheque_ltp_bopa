<?php
require_once 'config.php';

$matieres = $pdo->query("
    SELECT m.*
    FROM  matieres m 
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Gestion des Matieres - Admin</title>
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
                    <li><a href="parametre.php">⚙️ Paramètres</a></li>
                    <li><a href="logout.php">🚪 Déconnexion</a></li>
                </ul>
            </nav>
        </aside>
        <main class="admin-main">
            <div class="admin-header">
                <h1>Gestion des filières</h1>
                <a href="filiere_ajouter.php" class="btn-primary">➕ Nouvelle matière</a>
            </div>
           
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Filiere</th>
                        <th>Ordre</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matieres as $m): ?>
                    <tr>
                        <td><?= $m->id ?></td>
                        <td><?= htmlspecialchars($m->nom) ?></td>
                        <td><?= htmlspecialchars($m->filiere_id) ?></td>
                        <td><?= $m->ordre ?></td>
                        <td>
                            <a href="filiere_editer.php?id=<;?= $f->id ?>" class="btn-edit">✏️</a>
                            <a href="filiere_supprimer.php?id=<;?= $f->id ?>" class="btn-delete" onclick="return confirm('Supprimer cette filière supprimera aussi toutes ses matières et documents associés. Confirmer ?')">🗑️</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>