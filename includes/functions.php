<?php
// includes/functions.php


function cleanInput($data) {
    if (is_array($data)) {
        return array_map('cleanInput', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}


function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9]+/', '-', $string);
    $string = trim($string, '-');
    return $string;
}


function formatDate($date, $format = 'd/m/Y') {
    if (!$date) return '';
    $formatter = new IntlDateFormatter(
        'fr_FR',
        IntlDateFormatter::LONG,
        IntlDateFormatter::NONE
    );
    return $formatter->format(new DateTime($date));
}

function getDocumentTypeLabel($type) {
    $types = [
        'cours' => ['label' => 'Cours', 'icon' => '📚'],
        'td' => ['label' => 'Travaux Dirigés', 'icon' => '✏️'],
        'tp' => ['label' => 'Travaux Pratiques', 'icon' => '🔧'],
        'exercices' => ['label' => 'Exercices', 'icon' => '📝'],
        'corriges' => ['label' => 'Corrigés', 'icon' => '✅'],
        'fiche_technique' => ['label' => 'Fiche Technique', 'icon' => '📋'],
        'examen' => ['label' => 'Examen', 'icon' => '📄'],
        'autre' => ['label' => 'Autre', 'icon' => '📁']
    ];
    return $types[$type] ?? ['label' => $type, 'icon' => '📄'];
}


function getMessageTypeLabel($type) {
    $types = [
        'avis' => ['label' => 'Avis', 'icon' => '📢', 'color' => '#3498db'],
        'suggestion' => ['label' => 'Suggestion', 'icon' => '💡', 'color' => '#f39c12'],
        'plainte' => ['label' => 'Plainte', 'icon' => '⚠️', 'color' => '#e74c3c'],
        'question' => ['label' => 'Question', 'icon' => '❓', 'color' => '#2ecc71'],
        'temoignage' => ['label' => 'Témoignage', 'icon' => '💬', 'color' => '#9b59b6']
    ];
    return $types[$type] ?? ['label' => $type, 'icon' => '📌', 'color' => '#95a5a6'];
}


function displayBreadcrumb($items) {
    $html = '<nav class="breadcrumb" aria-label="Fil d\'Ariane">';
    $html .= '<ol class="breadcrumb-list">';
   
    foreach ($items as $index => $item) {
        if ($index < count($items) - 1) {
            $html .= '<li><a href="' . $item['url'] . '">' . $item['label'] . '</a></li>';
            $html .= '<li class="separator">›</li>';
        } else {
            $html .= '<li aria-current="page">' . $item['label'] . '</li>';
        }
    }
   
    $html .= '</ol></nav>';
    return $html;
}


function logAction($pdo, $page, $action, $document_id = null) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
   
    $stmt = $pdo->prepare("
        INSERT INTO logs_acces (page, ip_address, user_agent, document_id, action)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$page, $ip, $user_agent, $document_id, $action]);
}


function updateDailyStats($pdo, $type) {
    $today = date('Y-m-d');
   
    $stmt = $pdo->prepare("
        INSERT INTO statistiques_journalieres (date_jour, {$type}_total)
        VALUES (?, 1)
        ON DUPLICATE KEY UPDATE {$type}_total = {$type}_total + 1
    ");
    $stmt->execute([$today]);
}


function isAdmin() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function redirect($url) {
    header("Location: $url");
    exit;
}


function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
* Vérifie le token CSRF
*/
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
