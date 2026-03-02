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
    if (empty($titre) || empty($type_document) || $matiere_id <= 0) {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } elseif (!isset($_FILES['fichier']) || $_FILES['fichier']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Veuillez sélectionner un fichier valide.';
    } else {
        $file = $_FILES['fichier'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'docx', 'pptx', 'xlsx', 'txt', 'jpg', 'png'];
        if (!in_array($ext, $allowed)) {
            $error = 'Format de fichier non autorisé. Formats acceptés : ' . implode(', ', $allowed);
        } elseif ($file['size'] > 10 * 1024 * 1024) { // 10 Mo max
            $error = 'Le fichier ne doit pas dépasser 10 Mo.';
        } else {
            // Générer un nom unique
            $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
            $upload_dir = '../assets/uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
           
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
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un document - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="admin.css">
    <script>
    function updateMatieres() {
        const filiereId = document.getElementById('filiere').value;
        const matiereSelect = document.getElementById('matiere_id');
        matiereSelect.innerHTML = '<option value="">Chargement...</option>';
       
        fetch('ajax_get_matieres.php?filiere_id=' + filiereId)
            .then(response => response.json())
            .then(data => {
                matiereSelect.innerHTML = '<option value="">Sélectionnez une matière</option>';
                data.forEach(m => {
                    const option = document.createElement('option');
                    option.value = m.id;
                    option.textContent = m.nom;
                    matiereSelect.appendChild(option);
                });
            });
    }
    </script>
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
            <h1>Ajouter un document</h1>
           
            <?php if ($error): ?><div class="alert error"><?= $error ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert success"><?= $success ?></div><?php endif; ?>
           
            <form method="POST" enctype="multipart/form-data" class="admin-form">
                <div class="form-group">
                    <label for="titre">Titre *</label>
                    <input type="text" id="titre" name="titre" required>
                </div>
               
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"></textarea>
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
                   
                    <div class="form-group">
                        <label for="matiere_id">Matière *</label>
                        <select id="matiere_id" name="matiere_id" required>
                            <option value="">D'abord choisir une filière</option>
                        </select>
                    </div>
                </div>
               
                <div class="form-row">
                    <div class="form-group">
                        <label for="type_document">Type de document *</label>
                        <select id="type_document" name="type_document" required>
                            <option value="cours">Cours</option>
                            <option value="td">TD</option>
                            <option value="tp">TP</option>
                            <option value="exercices">Exercices</option>
                            <option value="corriges">Corrigés</option>
                            <option value="fiche_technique">Fiche technique</option>
                            <option value="examen">Examen</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                   
                    <div class="form-group">
                        <label for="auteur">Auteur</label>
                        <input type="text" id="auteur" name="auteur">
                    </div>
                </div>
               
                <div class="form-group">
                    <label for="annee_scolaire">Année scolaire</label>
                    <input type="text" id="annee_scolaire" name="annee_scolaire" placeholder="ex: 2025-2026">
                </div>
               
                <div class="form-group">
                    <label for="fichier">Fichier * (max 10 Mo, formats: pdf, docx, pptx, xlsx, txt, jpg, png)</label>
                    <input type="file" id="fichier" name="fichier" required>
                </div>
               
                <button type="submit" class="btn-primary">Enregistrer</button>
                <a href="documents.php" class="btn-secondary">Annuler</a>
            </form>
        </main>
    </div>
</body>
</html>
