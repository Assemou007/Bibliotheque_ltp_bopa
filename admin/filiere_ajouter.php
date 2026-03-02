<?php
// admin/filiere_ajouter.php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = cleanInput($_POST['nom'] ?? '');
    $description = cleanInput($_POST['description'] ?? '');
    $icone = cleanInput($_POST['icone'] ?? '📚');
    $couleur = cleanInput($_POST['couleur'] ?? '#3498db');
    $ordre = (int)($_POST['ordre'] ?? 0);
    $slug = createSlug($nom);
   
    if (empty($nom)) {
        $error = 'Le nom est requis.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO filieres (nom, slug, description, icone, couleur, ordre) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $slug, $description, $icone, $couleur, $ordre]);
        header('Location: filieres.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ajouter une filière - Admin</title>
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
            <h1>Ajouter une filière</h1>
           
            <?php if (isset($error)): ?><div class="alert error"><?= $error ?></div><?php endif; ?>
           
            <form method="POST" class="admin-form">
                <div class="form-group">
                    <label for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom" required>
                </div>
               
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>
               
                <div class="form-row">
                    <div class="form-group">
                        <label for="icone">Icône (emoji)</label>
                        <input type="text" id="icone" name="icone" value="📚" maxlength="10">
                    </div>
                    <div class="form-group">
                        <label for="couleur">Couleur (hex)</label>
                        <input type="color" id="couleur" name="couleur" value="#3498db">
                    </div>
                    <div class="form-group">
                        <label for="ordre">Ordre d'affichage</label>
                        <input type="number" id="ordre" name="ordre" value="0" min="0">
                    </div>
                </div>
               
                <button type="submit" class="btn-primary">Enregistrer</button>
                <a href="filieres.php" class="btn-secondary">Annuler</a>
            </form>
        </main>
    </div>
</body>
</html>
