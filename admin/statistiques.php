<?php
// admin/index.php
require_once 'config.php';

// Récupérer quelques statistiques
$stats = [
    'documents' => $pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn(),
    'filieres' => $pdo->query("SELECT COUNT(*) FROM filieres")->fetchColumn(),
    'matieres' => $pdo->query("SELECT COUNT(*) FROM matieres")->fetchColumn(),
    'utilisateur' => $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn(),
    'messages_attente' => $pdo->query("SELECT COUNT(*) FROM messages_publics WHERE statut = 'en_attente'")->fetchColumn(),
    'contacts_non_lus' => $pdo->query("SELECT COUNT(*) FROM contacts WHERE lu = 0")->fetchColumn(),
    'documents_populaires' => $pdo->query("SELECT SUM(vue_count) FROM documents")->fetchColumn(),
];

$derniers_messages = $pdo->query("
    SELECT * FROM messages_publics
    WHERE statut = 'en_attente'
    ORDER BY created_at DESC
    LIMIT 5
")->fetchAll();

$docs_par_filiere = $pdo->query("
    SELECT f.nom, f.couleur, COUNT(d.id) as count
    FROM filieres f
    LEFT JOIN matieres m ON f.id = m.filiere_id
    LEFT JOIN documents d ON m.id = d.matiere_id AND d.est_public = 1
    GROUP BY f.id
    ORDER BY count DESC
")->fetchAll();

$derniers_contacts = $pdo->query("
    SELECT * FROM contacts
    WHERE lu = 0
    ORDER BY created_at DESC
    LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - LTP-BOPA</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
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
       
        <!-- Main content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>Dashboard</h1>
                <div class="admin-user">
                    Bienvenue, <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>
                </div>
            </div>
           
            <div class="admin-stats">
                <div class="stat-card">
                    <div class="stat-icon">📄</div>
                    <div class="stat-detail">
                        <span class="stat-value"><?= $stats['documents'] ?></span>
                        <span class="stat-label">Documents</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🏫</div>
                    <div class="stat-detail">
                        <span class="stat-value"><?= $stats['filieres'] ?></span>
                        <span class="stat-label">Filières</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📘</div>
                    <div class="stat-detail">
                        <span class="stat-value"><?= $stats['matieres'] ?></span>
                        <span class="stat-label">Matières</span>
                    </div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-detail">
                        <span class="stat-value"><?= $stats['messages_attente'] ?></span>
                        <span class="stat-label">Messages en attente</span>
                    </div>
                </div>
                <div class="stat-card info">
                    <div class="stat-icon">📧</div>
                    <div class="stat-detail">
                        <span class="stat-value"><?= $stats['contacts_non_lus'] ?></span>
                        <span class="stat-label">Contacts non lus</span>
                    </div>
                </div>
                <div class="stat-card success">
                    <div class="stat-icon">👁️</div>
                    <div class="stat-detail">
                        <span class="stat-value"><?= number_format($stats['documents_populaires'] ?? 0) ?></span>
                        <span class="stat-label">Vues totales</span>
                    </div>
                </div>
                <div class="stat-card success">
                    <div class="stat-icon"></div>
                    <div class="stat-detail">
                        <span class="stat-value"><?= number_format($stats['documents_populaires'] ?? 0) ?></span>
                        <span class="stat-label">Vues totales</span>
                    </div>
                </div>
            </div>
           
               <div class="stats-chart-card">
            <h2>📚 Documents par filière</h2>
            <div class="bar-chart">
                <?php foreach ($docs_par_filiere as $filiere):
                    $max = max(array_column($docs_par_filiere, 'count'));
                    $largeur = $max > 0 ? ($filiere->count / $max) * 100 : 0;
                ?>
                <div class="bar-item">
                    <span class="bar-label" style="color: <?= $filiere->couleur ?>">
                        <?= htmlspecialchars(substr($filiere->nom, 0, 30)) ?>...
                    </span>
                    <div class="bar-container">
                        <div class="bar" style="width: <?= $largeur ?>%; background-color: <?= $filiere->couleur ?>">
                            <span class="bar-value"><?= $filiere->count ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>     
    </div>
</body>
</html>