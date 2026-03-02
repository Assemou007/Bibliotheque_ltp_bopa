<?php
// pages/faq.php

// Récupérer les catégories
$categories = $pdo->query("
    SELECT * FROM faq_categories
    ORDER BY ordre
")->fetchAll();

// Récupérer les questions par catégorie
$faq_par_categorie = [];
foreach ($categories as $cat) {
    $stmt = $pdo->prepare("
        SELECT * FROM faq_entries
        WHERE categorie_id = ? AND est_active = 1
        ORDER BY priorite DESC, question ASC
    ");
    $stmt->execute([$cat->id]);
    $faq_par_categorie[$cat->id] = [
        'categorie' => $cat,
        'questions' => $stmt->fetchAll()
    ];
}

// Recherche dans la FAQ
$search_results = [];
$search_query = isset($_GET['q']) ? cleanInput($_GET['q']) : '';

if (!empty($search_query) && strlen($search_query) >= 3) {
    $stmt = $pdo->prepare("
        SELECT f.*, c.nom as categorie_nom
        FROM faq_entries f
        LEFT JOIN faq_categories c ON f.categorie_id = c.id
        WHERE f.est_active = 1
          AND (f.question LIKE ? OR f.reponse LIKE ? OR f.mots_cles LIKE ?)
        ORDER BY f.priorite DESC
    ");
    $search_term = '%' . $search_query . '%';
    $stmt->execute([$search_term, $search_term, $search_term]);
    $search_results = $stmt->fetchAll();
}

logAction($pdo, 'faq', 'vue');
?>

<div class="faq-page">
    <div class="faq-header">
        <h1>❓ Foire Aux Questions</h1>
        <p class="subtitle">Trouvez rapidement des réponses à vos questions</p>
    </div>
   
    <!-- Barre de recherche FAQ -->
    <div class="faq-search">
        <form action="" method="get" class="search-form">
            <input type="hidden" name="page" value="faq">
            <div class="search-wrapper">
                <input type="search"
                       name="q"
                       placeholder="Rechercher dans la FAQ..."
                       value="<?= htmlspecialchars($search_query) ?>">
                <button type="submit">🔍 Rechercher</button>
            </div>
        </form>
    </div>
   
    <?php if (!empty($search_query)): ?>
        <!-- Résultats de recherche -->
        <div class="search-results">
            <h2>Résultats pour "<?= htmlspecialchars($search_query) ?>"</h2>
           
            <?php if (empty($search_results)): ?>
                <p class="no-results">Aucun résultat trouvé. Essayez avec d'autres mots-clés.</p>
            <?php else: ?>
                <div class="results-list">
                    <?php foreach ($search_results as $result): ?>
                    <div class="result-item">
                        <h3>
                            <a href="#faq-<?= $result->id ?>" onclick="showQuestion(<?= $result->id ?>)">
                                <?= htmlspecialchars($result->question) ?>
                            </a>
                        </h3>
                        <p class="result-categorie">Catégorie: <?= htmlspecialchars($result->categorie_nom) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
   
    <!-- FAQ par catégorie -->
    <div class="faq-categories">
        <?php foreach ($faq_par_categorie as $cat_id => $data):
            if (empty($data['questions'])) continue;
        ?>
        <div class="faq-category">
            <h2><?= htmlspecialchars($data['categorie']->nom) ?></h2>
            <?php if ($data['categorie']->description): ?>
                <p class="category-description"><?= htmlspecialchars($data['categorie']->description) ?></p>
            <?php endif; ?>
           
            <div class="faq-list">
                <?php foreach ($data['questions'] as $faq): ?>
                <div class="faq-item" id="faq-<?= $faq->id ?>">
                    <div class="faq-question" onclick="toggleAnswer(this)">
                        <h3><?= htmlspecialchars($faq->question) ?></h3>
                        <span class="toggle-icon">▼</span>
                    </div>
                    <div class="faq-answer">
                        <?= nl2br(htmlspecialchars($faq->reponse)) ?>
                       
                        <?php if ($faq->vue_count > 0): ?>
                        <div class="faq-meta">
                            <span class="vue-count">👁️ <?= $faq->vue_count ?> consultations</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
   
    <!-- Section "Pas de réponse" -->
    <div class="faq-footer">
        <p>Vous n'avez pas trouvé réponse à votre question ?</p>
        <div class="actions">
            <a href="index.php?page=contact" class="btn-primary">📧 Contactez-nous</a>
            <a href="index.php?page=messages" class="btn-secondary">💬 Espace public</a>
        </div>
    </div>
</div>

<script>
function toggleAnswer(element) {
    const answer = element.nextElementSibling;
    const icon = element.querySelector('.toggle-icon');
   
    if (answer.style.display === 'none' || !answer.style.display) {
        answer.style.display = 'block';
        icon.textContent = '▲';
    } else {
        answer.style.display = 'none';
        icon.textContent = '▼';
    }
}

function showQuestion(id) {
    const element = document.getElementById('faq-' + id);
    if (element) {
        element.scrollIntoView({behavior: 'smooth'});
        const answer = element.querySelector('.faq-answer');
        const icon = element.querySelector('.toggle-icon');
        answer.style.display = 'block';
        icon.textContent = '▲';
    }
}

// Ouvrir la question si elle vient d'une recherche
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash) {
        const id = window.location.hash.replace('#faq-', '');
        if (id) {
            showQuestion(id);
        }
    }
});
</script>