<?php
// document.php - Gère la consultation et le téléchargement des documents avec comptage sûr
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
$doc = $stmt->fetch(PDO::FETCH_OBJ);

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

// Incrémenter le compteur selon l'action - SAFE COALESCE
try {
    if ($action === 'view') {
        $update_stmt = $pdo->prepare("UPDATE documents SET vue_count = COALESCE(vue_count, 0) + 1 WHERE id = ?");
        $update_stmt->execute([$id]);
    } elseif ($action === 'download') {
        $update_stmt = $pdo->prepare("UPDATE documents SET telechargement_count = COALESCE(telechargement_count, 0) + 1 WHERE id = ?");
        $update_stmt->execute([$id]);
    }
    // Stats optionnels - non bloquants
    @logAction($pdo, 'document_'.$action, $action, $id);
    @updateDailyStats($pdo, $action.'s_total');
} catch (PDOException $e) {
    error_log("Document count error ID $id action $action: " . $e->getMessage());
}

// Serve document
try {
    if ($action === 'download') {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($doc->chemin_fichier) . '"');
        header('Content-Length: ' . filesize($file_path));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Transfer-Encoding: binary');
        readfile($file_path);
    } else {
        // View: force PDF inline or redirect
        $mime = mime_content_type($file_path);
        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . basename($doc->chemin_fichier) . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
    }
    exit;
} catch (Exception $e) {
    error_log("Document serve error ID $id action $action: " . $e->getMessage());
    header('HTTP/1.0 500 Internal Server Error');
    echo "Erreur serveur. Fichier corrompu ou introuvable.";
}
?>

