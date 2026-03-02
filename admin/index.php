<?php
// admin/index.php
require_once 'config.php';

// Récupérer quelques statistiques
$stats = [
    'documents' => $pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn(),
    'filieres' => $pdo->query("SELECT COUNT(*) FROM filieres")->fetchColumn(),
    'matieres' => $pdo->query("SELECT COUNT(*) FROM matieres")->fetchColumn(),
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
            </div>
           
            <div class="admin-grid">
                <!-- Messages en attente -->
                <div class="admin-card">
                    <div class="card-header">
                        <h2>Messages en attente de modération</h2>
                        <a href="messages.php" class="btn-small">Voir tout</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($derniers_messages)): ?>
                            <p class="text-muted">Aucun message en attente</p>
                        <?php else: ?>
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Auteur</th>
                                        <th>Titre</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($derniers_messages as $msg): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($msg->nom_utilisateur) ?></td>
                                        <td><?= htmlspecialchars($msg->titre) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($msg->created_at)) ?></td>
                                        <td>
                                            <a href="message_moderer.php?id=<;?= $msg->id ?>" class="btn-action">Modérer</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
               
                <!-- Contacts non lus -->
                <div class="admin-card">
                    <div class="card-header">
                        <h2>Contacts non lus</h2>
                        <a href="contacts.php" class="btn-small">Voir tout</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($derniers_contacts)): ?>
                            <p class="text-muted">Aucun contact non lu</p>
                        <?php else: ?>
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Sujet</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($derniers_contacts as $contact): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($contact->nom) ?></td>
                                        <td><?= htmlspecialchars($contact->sujet) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($contact->created_at)) ?></td>
                                        <td>
                                            <a href="contact_detail.php?id=<;?= $contact->id ?>" class="btn-action">Voir</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
           
            <!-- Actions rapides -->
            <div class="admin-quick-actions">
                <h2>Actions rapides</h2>
                <div class="quick-buttons">
                    <a href="document_ajouter.php" class="quick-btn btn-primary">➕ Ajouter un document</a>
                    <a href="filiere_ajouter.php" class="quick-btn btn-primary">➕ Ajouter une filière</a>
                    <a href="matiere_ajouter.php" class="quick-btn btn-primary">➕ Ajouter une matière</a>
                    <a href="faq_ajouter.php" class="quick-btn btn-primary">➕ Ajouter une FAQ</a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>