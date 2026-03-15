<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'nouveau_message') {
    // Activer l'affichage des erreurs pour debug (à enlever après)
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = "Erreur de sécurité. Veuillez réessayer.";
    } else {
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $type = $_POST['type'] ?? '';
        $titre = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');

        // Validation
        $errors = [];
        if (empty($nom)) $errors[] = "Le nom est requis.";
        if (empty($titre)) $errors[] = "Le titre est requis.";
        if (empty($contenu)) $errors[] = "Le message est requis.";
        if (!in_array($type, ['avis', 'suggestion', 'plainte', 'question', 'temoignage'])) {
            $errors[] = "Type de message invalide.";
        }
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email n'est pas valide.";
        }

        if (empty($errors)) {
            // Insertion en base
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $stmt = $pdo->prepare("
                INSERT INTO messages_publics (nom_utilisateur, email, type_message, titre, contenu, ip_address, statut)
                VALUES (?, ?, ?, ?, ?, ?, 'en_attente')
            ");
            if ($stmt->execute([$nom, $email, $type, $titre, $contenu, $ip])) {
                $success_message = "Votre message a été envoyé et sera visible après modération.";
                // On vide le formulaire
                $_POST = [];
            } else {
                $error_message = "Erreur lors de l'envoi. Veuillez réessayer.";
            }
        } else {
            $error_message = implode('<br>', $errors);
        }
    }
}

// Pagination
$page_num = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$per_page = 10;
$offset = ($page_num - 1) * $per_page;

// Compter le total des messages
$total = $pdo->query("
    SELECT COUNT(*) as count
    FROM messages_publics
    WHERE statut = 'approuve' AND parent_id IS NULL
")->fetch()->count;

$total_pages = ceil($total / $per_page);

// Récupérer les messages avec leurs réponses
$messages = $pdo->prepare("
    SELECT m.*,
           (SELECT COUNT(*) FROM messages_publics WHERE parent_id = m.id AND statut = 'approuve') as nb_reponses
    FROM messages_publics m
    WHERE m.statut = 'approuve' AND m.parent_id IS NULL
    ORDER BY
        CASE m.type_message
            WHEN 'plainte' THEN 1
            WHEN 'question' THEN 2
            WHEN 'suggestion' THEN 3
            WHEN 'avis' THEN 4
            ELSE 5
        END,
        m.created_at DESC
    LIMIT ? OFFSET ?
");
$messages->execute([$per_page, $offset]);
$messages = $messages->fetchAll();

// Récupérer les types de messages pour les filtres
$types_stats = $pdo->query("
    SELECT type_message, COUNT(*) as count
    FROM messages_publics
    WHERE statut = 'approuve' AND parent_id IS NULL
    GROUP BY type_message
    ORDER BY count DESC
")->fetchAll();

logAction($pdo, 'messages', 'vue');
?>

<div class="messages-page">
    <div class="page-header">
        <h1>💬 Espace d'expression publique</h1>
        <p class="page-description">
            Partagez vos avis, suggestions ou préoccupations concernant la bibliothèque numérique.
            Cet espace est modéré pour garantir des échanges constructifs.
        </p>
    </div>
   
    <div class="messages-layout">
        <!-- Colonne principale -->
        <div class="messages-main">
            <!-- Formulaire de publication -->
            <div class="message-form-card" id="nouveau-message">
                <h2>Publier un message</h2>
               
                <?php if ($success_message): ?>
                    <div class="alert success"><?= $success_message ?></div>
                <?php endif; ?>
               
                <?php if ($error_message): ?>
                    <div class="alert error"><?= $error_message ?></div>
                <?php endif; ?>
               
                <form method="POST" class="message-form">
                    <input type="hidden" name="action" value="nouveau_message">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                   
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom">Nom ou pseudo *</label>
                            <input type="text" id="nom" name="nom" required maxlength="100"
                                   value="<?= isset($_POST['nom']) ? cleanInput($_POST['nom']) : '' ?>">
                        </div>
                       
                        <div class="form-group">
                            <label for="email">Email (optionnel)</label>
                            <input type="email" id="email" name="email"
                                   value="<?= isset($_POST['email']) ? cleanInput($_POST['email']) : '' ?>">
                            <small>Pour recevoir une réponse personnalisée</small>
                        </div>
                    </div>
                   
                    <div class="form-row">
                        <div class="form-group">
                            <label for="type">Type de message *</label>
                            <select id="type" name="type" required>
                                <option value="avis" <?= (isset($_POST['type']) && $_POST['type'] == 'avis') ? 'selected' : '' ?>>📢 Avis / Témoignage</option>
                                <option value="suggestion" <?= (isset($_POST['type']) && $_POST['type'] == 'suggestion') ? 'selected' : '' ?>>💡 Suggestion d'amélioration</option>
                                <option value="question" <?= (isset($_POST['type']) && $_POST['type'] == 'question') ? 'selected' : '' ?>>❓ Question</option>
                                <option value="plainte" <?= (isset($_POST['type']) && $_POST['type'] == 'plainte') ? 'selected' : '' ?>>⚠️ Plainte / Problème</option>
                                <option value="temoignage" <?= (isset($_POST['type']) && $_POST['type'] == 'temoignage') ? 'selected' : '' ?>>💬 Témoignage</option>
                            </select>
                        </div>
                       
                        <div class="form-group">
                            <label for="titre">Titre *</label>
                            <input type="text" id="titre" name="titre" required maxlength="255"
                                   value="<?= isset($_POST['titre']) ? cleanInput($_POST['titre']) : '' ?>">
                        </div>
                    </div>
                   
                    <div class="form-group">
                        <label for="contenu">Votre message *</label>
                        <textarea id="contenu" name="contenu" required rows="5"><?= isset($_POST['contenu']) ? cleanInput($_POST['contenu']) : '' ?></textarea>
                    </div>
                   
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Publier mon message</button>
                    </div>
                </form>
            </div>
           
            <!-- Liste des messages -->
            <div class="messages-list-section">
                <h2>Messages récents</h2>
               
                <?php if (empty($messages)): ?>
                    <div class="no-messages">
                        <p>Aucun message pour le moment. Soyez le premier à donner votre avis !</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $msg):
                        $type = getMessageTypeLabel($msg->type_message);
                    ?>
                    <div class="message-thread" id="message-<?= $msg->id ?>">
                        <div class="message-card">
                            <div class="message-header">
                                <div class="message-type-badge" style="background-color: <?= $type['color'] ?>20; color: <?= $type['color'] ?>">
                                    <?= $type['icon'] ?> <?= $type['label'] ?>
                                </div>
                                <div class="message-author-info">
                                    <span class="message-author"><?= htmlspecialchars($msg->nom_utilisateur) ?></span>
                                    <span class="message-date">📅 <?= date('d/m/Y H:i', strtotime($msg->created_at)) ?></span>
                                </div>
                            </div>
                           
                            <h3 class="message-title"><?= htmlspecialchars($msg->titre) ?></h3>
                            <div class="message-content">
                                <?= nl2br(htmlspecialchars($msg->contenu)) ?>
                            </div>
                           
                            <?php if ($msg->nb_reponses > 0): ?>
                                <button class="show-replies-btn" onclick="toggleReplies(<?= $msg->id ?>)">
                                    <span class="btn-icon">💬</span>
                                    Voir les <?= $msg->nb_reponses ?> réponse(s)
                                </button>
                               
                                <div id="replies-<?= $msg->id ?>" class="replies-container" style="display: none;"></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                   
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page_num > 1): ?>
                            <a href="?page=messages&p=<?= $page_num - 1 ?>" class="page-link">← Précédent</a>
                        <?php endif; ?>
                       
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=messages&p=<?= $i ?>"
                               class="page-link <?= $i == $page_num ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                       
                        <?php if ($page_num < $total_pages): ?>
                            <a href="?page=messages&p=<?= $page_num + 1 ?>" class="page-link">Suivant →</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
       
        <!-- Sidebar avec statistiques -->
        <div class="messages-sidebar">
            <div class="sidebar-card">
                <h3>📊 Statistiques</h3>
                <div class="stats-list">
                    <div class="stat-row">
                        <span>Total messages:</span>
                        <strong><?= $total ?></strong>
                    </div>
                    <?php foreach ($types_stats as $stat):
                        $type = getMessageTypeLabel($stat->type_message);
                    ?>
                    <div class="stat-row">
                        <span><?= $type['icon'] ?> <?= $type['label'] ?>:</span>
                        <strong><?= $stat->count ?></strong>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
           
            <div class="sidebar-card">
                <h3>📝 Règles de publication</h3>
                <ul class="rules-list">
                    <li>✓ Restez courtois et respectueux</li>
                    <li>✓ Pas de propos injurieux ou diffamatoires</li>
                    <li>✓ Messages modérés avant publication</li>
                    <li>✓ Un email valide pour une réponse personnalisée</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function toggleReplies(messageId) {
    const container = document.getElementById('replies-' + messageId);
    const button = event.target.closest('.show-replies-btn');
   
    if (container.style.display === 'none') {
        // Charger les réponses
        fetch('api/messages.php?action=get_replies&id=' + messageId)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.replies.length > 0) {
                    container.innerHTML = data.replies.map(reply => `
                        <div class="reply-card">
                            <div class="reply-header">
                                <span class="reply-author">${reply.nom_utilisateur}</span>
                                <span class="reply-date">📅 ${reply.date_creation}</span>
                            </div>
                            <div class="reply-content">${reply.contenu}</div>
                        </div>
                    `).join('');
                }
                container.style.display = 'block';
                button.innerHTML = '<span class="btn-icon">💬</span> Masquer les réponses';
            });
    } else {
        container.style.display = 'none';
        button.innerHTML = '<span class="btn-icon">💬</span> Voir les réponses';
    }
}
</script>