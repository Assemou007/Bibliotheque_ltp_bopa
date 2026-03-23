<?php
// admin/document_ajouter.php
require_once 'config.php';

$error = '';
$success = '';

// Récupérer les filières et matières pour les sélecteurs
$filieres = $pdo->query("SELECT * FROM filieres ORDER BY nom")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = cleanInput($_POST['titre'] ?? '');
    $description = cleanInput($_POST['description'] ?? '');
    $type_document = cleanInput($_POST['type_document'] ?? '');
    $matiere_id = (int)($_POST['matiere_id'] ?? 0);
    $auteur = cleanInput($_POST['auteur'] ?? '');
    $annee_scolaire = cleanInput($_POST['annee_scolaire'] ?? '');
   
    // Validation
    if (empty($titre) || empty($type_document) || $filiere_id <= 0) {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    }
          
       if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
                // Insérer en base
                $stmt = $pdo->prepare("
                    INSERT INTO documents (titre, description, type_document, format_fichier, chemin_fichier, matiere_id, auteur, annee_scolaire)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $titre,
                    $description,
                    $type_document,
                    $ext,
                    $filename,
                    $matiere_id,
                    $auteur,
                    $annee_scolaire
                ]);
                $success = 'Document ajouté avec succès.';
            } else {
                $error = 'Erreur lors de l\'upload du fichier.';
            }
        }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un document - Admin</title>
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
            <h1>Ajouter une Matiere</h1>
           
            <?php if ($error): ?><div class="alert error"><?= $error ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert success"><?= $success ?></div><?php endif; ?>
           
            <form method="POST" enctype="multipart/form-data" class="admin-form">
                <div class="form-group">
                    <label for="nom">Nom *</label>
                    <input type="text" id="titre" name="nom" required>
                </div>
               
                <div class="form-group">
                    <label for="description">Slug</label>
                    <input type="text" id="slug" name="slug" required>
                </div>
               
                <div class="form-row">
                    <div class="form-group">
                        <label for="filiere">Filière *</label>
                        <select id="filiere" onchange="updateMatieres()" required>
                            <option value="">Sélectionnez</option>
                            <?php foreach ($filieres as $f): ?>
                            <option value="<?= $f->id ?>"><?= htmlspecialchars($f->nom) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
           
                    </div>               
                <button type="submit" class="btn-primary">Enregistrer</button>
                <a href="documents.php" class="btn-secondary">Annuler</a>
            </form>
        </main>
    </div>
</body>
</html>
