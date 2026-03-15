<?php
// pages/inscription.php
require_once 'config/database.php';
require_once 'includes/functions.php';
requireGuest(); // Redirige si déjà connecté

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($nom) || empty($email) || empty($password)) {
        $error = 'Tous les champs sont requis.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide.';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } elseif ($password !== $confirm) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Cet email est déjà utilisé.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)");
            if ($stmt->execute([$nom, $email, $hash])) {
                $success = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
                // Optionnel : connecter directement
                // $_SESSION['user_id'] = $pdo->lastInsertId();
                // header('Location: index.php?page=dashboard');
                // exit;
            } else {
                $error = 'Erreur lors de l\'inscription.';
            }
        }
    }
}
?>
<div class="auth-page">
    <h1>Inscription</h1>
    <?php if ($error): ?><div class="alert error"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert success"><?= $success ?></div><?php endif; ?>
    <form method="POST" class="auth-form">
        <div class="form-group">
            <label for="nom">Nom complet</label>
            <input type="text" id="nom" name="nom" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirmer le mot de passe</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn-primary">S'inscrire</button>
    </form>
    <p class="auth-link">Déjà inscrit ? <a href="index.php?page=connexion">Connectez-vous</a></p>
</div>