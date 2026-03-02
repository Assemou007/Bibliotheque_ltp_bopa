<?php
// api/chat.php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../includes/functions.php';

session_start();

$input = json_decode(file_get_contents('php://input'), true);
$question = isset($input['question']) ? cleanInput($input['question']) : '';

if (empty($question)) {
    echo json_encode(['reponse' => 'Veuillez poser une question.']);
    exit;
}

// Fonction intelligente pour trouver la meilleure réponse
function trouverReponse($pdo, $question) {
    $question = strtolower($question);
   
    // Recherche dans la FAQ
    $stmt = $pdo->prepare("
        SELECT reponse, priorite
        FROM faq_entries
        WHERE est_active = 1
          AND (question LIKE ? OR reponse LIKE ? OR mots_cles LIKE ?)
        ORDER BY priorite DESC,
                 CASE
                     WHEN question LIKE ? THEN 3
                     WHEN mots_cles LIKE ? THEN 2
                     ELSE 1
                 END DESC
        LIMIT 1
    ");
   
    $search_term = '%' . $question . '%';
    $stmt->execute([$search_term, $search_term, $search_term, $search_term, $search_term]);
    $result = $stmt->fetch();
   
    if ($result) {
        // Incrémenter le compteur de vues
        $pdo->prepare("UPDATE faq_entries SET vue_count = vue_count + 1 WHERE question LIKE ?")
            ->execute([$search_term]);
        return $result->reponse;
    }
   
    // Vérifier si la question concerne une filière spécifique
    $filieres = $pdo->query("SELECT nom FROM filieres")->fetchAll();
    foreach ($filieres as $filiere) {
        if (strpos($question, strtolower($filiere->nom)) !== false) {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as nb_docs
                FROM documents d
                JOIN matieres m ON d.matiere_id = m.id
                JOIN filieres f ON m.filiere_id = f.id
                WHERE f.nom = ?
            ");
            $stmt->execute([$filiere->nom]);
            $count = $stmt->fetch()->nb_docs;
           
            return "La filière {$filiere->nom} propose actuellement {$count} documents répartis dans différentes matières. Vous pouvez les consulter depuis la page des filières.";
        }
    }
   
    // Réponse par défaut contextuelle
    return "Je n'ai pas trouvé de réponse spécifique à votre question. Voici quelques suggestions :\n\n" .
           "📋 Consultez notre [FAQ](?page=faq) pour les questions fréquentes\n" .
           "💬 Laissez un message dans l'[espace public](?page=messages)\n" .
           "📧 Contactez-nous via le [formulaire de contact](?page=contact)";
}

$reponse = trouverReponse($pdo, $question);

// Sauvegarder dans l'historique
$session_id = session_id();
$stmt = $pdo->prepare("INSERT INTO chat_history (session_id, question, reponse) VALUES (?, ?, ?)");
$stmt->execute([$session_id, $question, $reponse]);

echo json_encode(['reponse' => $reponse]);
?>