<?php
// admin/faq.php - Gestion complète des FAQ depuis faq_entries
require_once 'config.php';

$message = '';
$error = '';

// Traitement suppression
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM faq_entries WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'FAQ supprimée avec succès.';
    } catch (PDOException $e) {
        $error = 'Erreur suppression: ' . $e->getMessage();
    }
}

// Traitement formulaire (ajout/modification)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['question'] ?? '');
    $reponse = trim($_POST['reponse'] ?? '');
    
    if (empty($question) || empty($reponse)) {
        $error = 'Question et réponse requises.';
    } else {
        try {
            if (isset($_POST['id']) && $_POST['id']) {
                // Update
                $id = (int)$_POST['id'];
                $stmt = $pdo->prepare("UPDATE faq_entries SET question = ?, reponse = ? WHERE id = ?");
                $stmt->execute([$question, $reponse, $id]);
                $message = 'FAQ mise à jour !';
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO faq_entries (question, reponse, mots_cles, est_active) VALUES (?, ?, '', 1)");
                $stmt->execute([$question, $reponse]);
                $message = 'FAQ ajoutée !';
            }
        } catch (PDOException $e) {
            $error = 'Erreur BD: ' . $e->getMessage();
        }
    }
}

// Edition - charger FAQ
$faq_edit = null;
if (isset($_GET['edit']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM faq_entries WHERE id = ?");
        $stmt->execute([$id]);
        $faq_edit = $stmt->fetch();
    } catch (PDOException $e) {
        $error = 'FAQ non trouvée.';
    }
}

// Liste FAQ
try {
    $faqs = $pdo->query("SELECT * FROM faq_entries WHERE est_active = 1 ORDER BY priorite DESC, id DESC")->fetchAll();
} catch (PDOException $e) {
    $error = 'Table faq_entries introuvable ou erreur BD. ' . $e->getMessage();
    $faqs = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>FAQ Admin - LTP-BOPA</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
            <h1><i class="fas fa-question-circle"></i> Gestion FAQ</i></h1>
            
            <!-- Messages -->
            <?php if ($message): ?>
                <div class="alert success">
                    <i class="fas fa-check"></i> <?= $message ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire -->
            <div class="admin-card">
                <div class="card-header">
                    <h3><i class="fas fa-edit"></i> <?= $faq_edit ? 'Modifier FAQ' : 'Ajouter FAQ' ?></h3>
                    <?php if (!$faq_edit): ?>
                        <button onclick="toggleForm()" class="btn-secondary">
                            <i class="fas fa-eye-slash"></i> Masquer
                        </button>
                    <?php endif; ?>
                </div>
                <form method="POST" class="admin-form">
                    <input type="hidden" name="id" value="<?= $faq_edit->id ?? '' ?>">
                    <div class="form-group">
                        <label>Question <span class="required">*</span></label>
                        <textarea name="question" required rows="2" placeholder="Ex: Comment s'inscrire ?"><?= htmlspecialchars($faq_edit->question ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Réponse <span class="required">*</span></label>
                        <textarea name="reponse" required rows="5" placeholder="Réponse détaillée..."><?= htmlspecialchars($faq_edit->reponse ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> <?= $faq_edit ? 'Modifier' : 'Ajouter' ?>
                    </button>
                </form>
            </div>

            <!-- Liste -->
            <div class="admin-card">
                <h3>FAQ actives (<?= count($faqs) ?>)</h3>
                <?php if (empty($faqs)): ?>
                    <div class="empty-state">
                        <i class="fas fa-question" style="font-size: 4rem; color: #dee2e6;"></i>
                        <p>Aucune FAQ active</p>
                        <p>Créez la première ci-dessus !</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Question</th>
                                    <th>Réponse (extrait)</th>
                                    <th>Priorité</th>
                                    <th>Vues</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($faqs as $faq): ?>
                                <tr>
                                    <td>#<?= $faq->id ?></td>
                                    <td><?= htmlspecialchars(substr($faq->question, 0, 40)) ?>...</td>
                                    <td><?= htmlspecialchars(substr($faq->reponse, 0, 40)) ?>...</td>
                                    <td><span class="badge"><?= $faq->priorite ?></span></td>
                                    <td><?= $faq->vue_count ?></td>
                                    <td><?= date('d/m H:i', strtotime($faq->created_at)) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="?edit&id=<?= $faq->id ?>" class="btn-action btn-edit" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?delete&id=<?= $faq->id ?>" class="btn-action btn-delete" onclick="return confirm('Confirmer suppression?')" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
    function toggleForm() {
        const form = document.querySelector('.admin-form');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
    </script>
</body>
</html>

*Note: Interface complète avec table `faq_entries`. Fonctionne avec vos colonnes (question, reponse, priorite, vue_count, est_active). Testez maintenant!*
