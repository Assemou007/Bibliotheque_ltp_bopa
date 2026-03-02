<?php
// config/database.php

$host = 'localhost';
$dbname = 'biblio';
$username = 'root';
$password = '';
 
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    error_log("Erreur de connexion: " . $e->getMessage());
    die("Désolé, une erreur technique est survenue. Veuillez réessayer plus tard.");
}

// Démarrer la session si pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurer le fuseau horaire
date_default_timezone_set('Africa/Porto-Novo');
?>