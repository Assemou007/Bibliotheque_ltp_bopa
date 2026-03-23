<?php
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ? AND est_actif = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        if ($user && password_verify($password, $user->mot_de_passe)) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_nom'] = $user->nom;
            $_SESSION['user_email'] = $user->email;
            $_SESSION['user_role'] = $user->role;
            $pdo->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = ?")->execute([$user->id]);
            header('Location: index.php?page=dashboard');
            exit;
        } else {
            $error = 'Identifiants incorrects.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - LTP-BOPA Bibliothèque</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="auth-page">
    <main class="auth-main">
        <div class="auth-panels">
            <!-- Panneau gauche: Formulaire -->
            <div class="panel form-panel">
                <div class="logo">
                    <i class="fas fa-graduation-cap"></i>
                    <h1>LTP-BOPA</h1>
                </div>
                <h2>Connexion</h2>
                <?php if ($error): ?>
                    <div class="alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="input-group">
                        <input type="email" name="email" required placeholder="Email">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" required placeholder="Mot de passe">
                        <i class="fas fa-lock"></i>
                    </div>
                    <label class="checkbox">
                        <input type="checkbox" name="remember">
                        Se souvenir de moi
                        <span class="checkmark"></span>
                    </label>
                    <button type="submit">Se connecter</button>
                </form>
                <p>Pas de compte? <a href="index.php?page=inscription">S'inscrire</a></p>
            </div>
            <!-- Panneau droit: Description -->
            <div class="panel info-panel">
                <h2>Bibliothèque LTP-BOPA</h2>
                <p>Plateforme officielle Lycée Technique de la Montagne</p>
                <div class="features">
                    <div class="feature">
                        <i class="fas fa-book"></i>
                        <span>1000+ documents</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-users"></i>
                        <span>Communauté active</span>
                    </div>
                    <div class="stats">
                        <div>500+</div>
                        <div>Étudiants</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <style>
.auth-page {
    margin: 0;
    padding: 40px 20px;
    background: linear-gradient(135deg, #1e2b4f 0%, #2a3b6b 50%, #667eea 100%);
    min-height: 100vh;
    font-family: Inter, sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: auto;
}

.auth-main {
    width: 100%;
    max-width: 900px;
}


.auth-panels {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1px;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(20px);
    height: 600px;
    max-height: 90vh;
}

@media (max-width: 900px) {
    grid-template-columns: 1fr !important;
    height: auto !important;
}


.panel {
    padding: 60px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.form-panel {
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(20px);
}

.info-panel {
    background: rgba(30,43,79,0.8);
    backdrop-filter: blur(20px);
    color: white;
    position: relative;
}

.logo {
    text-align: center;
    margin-bottom: 30px;
}

.logo i {
    font-size: 3rem;
    color: #1e2b4f;
    margin-bottom: 10px;
    display: block;
}

.logo h1 {
    color: #1e2b4f;
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
}

h2 {
    font-size: 2rem;
    margin-bottom: 24px;
    text-align: center;
}

.form-panel h2 {
    color: #1e2b4f;
}

.info-panel h2 {
    color: white;
}

.alert-error {
    background: #fee;
    border-left: 4px solid #f66;
    color: #c33;
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 24px;
}

.input-group {
    position: relative;
    margin-bottom: 20px;
}


.input-group input {
    width: 100%;
    padding: 16px 16px 16px 48px !important;
    box-sizing: border-box;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s;
    background: white;
}


.input-group input:focus {
    outline: none;
    border-color: #1e2b4f;
    box-shadow: 0 0 0 3px rgba(30,43,79,0.1);
}

.input-group i {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 1.1rem;
}

.checkbox {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
    cursor: pointer;
    color: #475569;
    font-size: 0.95rem;
}


.checkbox {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 24px;
    cursor: pointer;
    color: #475569;
    font-size: 0.95rem;
}

.checkbox input {
    accent-color: #1e2b4f;
    margin-top: 2px;
    flex-shrink: 0;
}

.checkmark {
    width: 20px;
    height: 20px;
    border: 2px solid #e2e8f0;
    border-radius: 4px;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}

.checkbox input:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-weight: bold;
    font-size: 12px;
}


button {
    width: 100%;
    padding: 18px;
    background: linear-gradient(135deg, #1e2b4f, #2a3b6b);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 8px 25px rgba(30,43,79,0.3);
    margin-bottom: 24px;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 35px rgba(30,43,79,0.4);
}

p {
    text-align: center;
    color: #64748b;
    margin: 0;
    font-size: 0.95rem;
}

p a {
    color: #1e2b4f;
    font-weight: 600;
    text-decoration: none;
}

p a:hover {
    color: #ffb347;
}

.features {
    display: flex;
    flex-direction: column;
    gap: 24px;
    margin-bottom: 32px;
}

.feature {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: rgba(255,255,255,0.1);
    border-radius: 16px;
    backdrop-filter: blur(10px);
}

.feature i {
    font-size: 1.8rem;
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.feature span {
    font-size: 1.1rem;
    font-weight: 500;
    opacity: 0.95;
}

.stats {
    display: flex;
    justify-content: space-around;
    margin-top: auto;
    padding-top: 32px;
    border-top: 1px solid rgba(255,255,255,0.2);
}

.stats div {
    text-align: center;
}

.stats div:first-child {
    font-size: 2.5rem;
    font-weight: 800;
    color: #ffb347;
}

.stats div:last-child {
    font-size: 0.85rem;
    opacity: 0.8;
    margin-top: 4px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

@media (max-width: 900px) {
    .auth-panels {
        grid-template-columns: 1fr;
        height: auto;
        max-height: 90vh;
    }

    .panel {
        padding: 40px;
    }
}

@media (max-width: 480px) {
    body {
        padding: 20px 10px;
    }

    .panel {
        padding: 30px 20px;
    }

    h1, h2 {
        font-size: 1.8rem;
    }
}
    </style>

