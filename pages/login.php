<?php
// pages/connexion.php
require_once 'config/database.php';
require_once 'includes/functions.php';
requireGuest();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ? AND est_actif = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user->mot_de_passe)) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_nom'] = $user->nom;
            $_SESSION['user_email'] = $user->email;
            $_SESSION['user_role'] = $user->role;

            // Mettre à jour la date de dernière connexion
            $pdo->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = ?")->execute([$user->id]);

            header('Location: index.php?page=dashboard');
            exit;
        } else {
            $error = 'Email ou mot de passe incorrect.';
        }
    }
}
?>
<div class="auth-page">
    <h1>Connexion</h1>
    <?php if ($error): ?><div class="alert error"><?= $error ?></div><?php endif; ?>
    <form method="POST" class="auth-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn-primary">Se connecter</button>
    </form>
    <p class="auth-link">Pas encore inscrit ? <a href="index.php?page=inscription">Créer un compte</a></p>
</div>