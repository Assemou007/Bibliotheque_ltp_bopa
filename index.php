<?php
// index.php

require_once 'config/database.php';
require_once 'includes/functions.php';

// Récupérer la page demandée
$page = isset($_GET['page']) ? cleanInput($_GET['page']) : 'accueil';
$page = $page ?: 'accueil'; // Si vide, accueil

// Sécuriser le nom de la page (éviter les inclusions malveillantes)
$page = preg_replace('/[^a-zA-Z0-9\-_]/', '', $page);

// Mapping des pages autorisées
$allowed_pages = [
    // Pages principales
    'accueil' => 'acceuil.php',
    'filiere' => 'filiere.php',
    'matiere' => 'matiere.php',
    'recherche' => 'recherche.php',
    'messages' => 'messages.php',
    'message-detail' => 'message-detail.php',
   
    // Pages informatives
    'a-propos' => 'a-propos.php',
    'contact' => 'contact.php',
    'faq' => 'faq.php',
    'guide' => 'guide.php',
    'statistiques' => 'statistiques.php',
    'documents-recents' => 'documents-recents.php',
    'documents-populaires' => 'documents-populaires.php',
    'plan-site' => 'plan-site.php',
    'accessibilite' => 'accessibilite.php',
    'mentions-legales' => 'mentions-legales.php',
    '404' => '404.php'
];

// Vérifier si la page est autorisée
if (!isset($allowed_pages[$page])) {
    $page = '404';
    header("HTTP/1.0 404 Not Found");
}

// Inclure l'en-tête
include 'includes/header.php';

// Inclure la page demandée
include 'pages/' . $allowed_pages[$page];


// Inclure le pied de page
include 'includes/footer.php';
?>
