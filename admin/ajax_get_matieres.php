<?php
// admin/ajax_get_matieres.php
require_once 'config.php';

$filiere_id = (int)($_GET['filiere_id'] ?? 0);
if ($filiere_id <= 0) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, nom FROM matieres WHERE filiere_id = ? ORDER BY nom");
$stmt->execute([$filiere_id]);
$matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($matieres);