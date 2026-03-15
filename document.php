<?php
// document.php - Gère la consultation et le téléchargement des documents avec comptage
require_once 'config/database.php';
require_once 'includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : 'view'; // 'view' ou 'download'

if (!$id) {
    header('HTTP/1.0 404 Not Found');
    include 'pages/404.php';
    exit;
}

// Récupérer le document
$stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ? AND est_public = 1");
$stmt->execute([$id]);
$doc = $stmt->fetch();

if (!$doc) {
    header('HTTP/1.0 404 Not Found');
    include 'pages/404.php';
    exit;
}

// Chemin complet du fichier
$file_path = __DIR__ . '/assets/uploads/' . $doc->chemin_fichier;
if (!file_exists($file_path)) {
    header('HTTP/1.0 404 Not Found');
    echo "Le fichier n'existe pas.";
    exit;
}

// Incrémenter le compteur selon l'action
if ($action === 'view') {
    
    $pdo->prepare("UPDATE documents SET vue_count = vue_count + 1 WHERE id = ?")->execute([$id]);
    logAction($pdo, 'document_view', 'vue', $id);
    updateDailyStats($pdo, 'vues_total');
} elseif ($action === 'download') {
    $pdo->prepare("UPDATE documents SET telechargement_count = telechargement_count + 1 WHERE id = ?")->execute([$id]);
    logAction($pdo, 'document_download', 'telechargement', $id);
    updateDailyStats($pdo, 'telechargements_total');
}

// Rediriger ou forcer le téléchargement
if ($action === 'download') {
    // Forcer le téléchargement
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($doc->chemin_fichier) . '"');
    header('Content-Length: ' . filesize($file_path));
    readfile($file_path);
    exit;
} else {
    // Redirection simple pour affichage dans le navigateur
    header('Location: assets/uploads/' . $doc->chemin_fichier);
    exit;
}