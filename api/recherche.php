<?php
// api/recherche.php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../includes/functions.php';

$q = isset($_GET['q']) ? cleanInput($_GET['q']) : '';

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

// Suggestions de titres de documents
$stmt = $pdo->prepare("
    SELECT titre FROM documents
    WHERE est_public = 1 AND titre LIKE ?
    ORDER BY vue_count DESC
    LIMIT 5
");
$stmt->execute(['%' . $q . '%']);
$documents = $stmt->fetchAll();

// Suggestions de matières
$stmt = $pdo->prepare("
    SELECT nom FROM matieres
    WHERE nom LIKE ?
    LIMIT 3
");
$stmt->execute(['%' . $q . '%']);
$matieres = $stmt->fetchAll();

// Fusionner les résultats
$suggestions = [];

foreach ($documents as $doc) {
    $suggestions[] = [
        'type' => 'document',
        'text' => $doc->titre
    ];
}

foreach ($matieres as $matiere) {
    $suggestions[] = [
        'type' => 'matiere',
        'text' => $matiere->nom
    ];
}

echo json_encode($suggestions);
?>