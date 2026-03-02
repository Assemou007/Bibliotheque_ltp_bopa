<?php
// api/messages.php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
   
    if ($_GET['action'] === 'get_replies' && isset($_GET['id'])) {
        $parent_id = (int)$_GET['id'];
       
        $stmt = $pdo->prepare("
            SELECT nom_utilisateur, contenu,
                   DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') as date_creation
            FROM messages_publics
            WHERE parent_id = ? AND statut = 'approuve'
            ORDER BY created_at ASC
        ");
        $stmt->execute([$parent_id]);
        $replies = $stmt->fetchAll();
       
        echo json_encode([
            'success' => true,
            'replies' => $replies
        ]);
        exit;
    }
   
    if ($_GET['action'] === 'search_suggestions' && isset($_GET['q'])) {
        $q = cleanInput($_GET['q']);
       
        $stmt = $pdo->prepare("
            SELECT titre FROM messages_publics
            WHERE statut = 'approuve' AND titre LIKE ?
            LIMIT 5
        ");
        $stmt->execute(['%' . $q . '%']);
        $suggestions = $stmt->fetchAll();
       
        echo json_encode([
            'success' => true,
            'suggestions' => $suggestions
        ]);
        exit;
    }
}

echo json_encode(['success' => false, 'error' => 'Requête invalide']);
?>