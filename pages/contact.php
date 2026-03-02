<?php
// pages/contact.php

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'envoyer_contact') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error_message = 'Erreur de sécurité. Veuillez réessayer.';
    } else {
        $nom = cleanInput($_POST['nom'] ?? '');
        $email = cleanInput($_POST['email'] ?? '');
        $sujet = cleanInput($_POST['sujet'] ?? '');
        $message = cleanInput($_POST['message'] ?? '');
       
        $errors = [];
        if (empty($nom)) $errors[] = "Le nom est requis";
        if (empty($email)) $errors[] = "L'email est requis";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide";
        if (empty($sujet)) $errors[] = "Le sujet est requis";
        if (empty($message)) $errors[] = "Le message est requis";
       
        if (empty($errors)) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $stmt = $pdo->prepare("
                INSERT INTO contacts (nom, email, sujet, message, ip_address)
                VALUES (?, ?, ?, ?, ?)
            ");
           
            if ($stmt->execute([$nom, $email, $sujet, $message, $ip])) {
                $success_message = "Votre message a été envoyé. Nous vous répondrons dans les plus brefs délais.";
               
                // Envoi d'email (optionnel)
                $to = "contact@ltp-bopa.bj";
                $headers = "From: $email\r\n";
                $headers .= "Reply-To: $email\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion();
                mail($to, "Contact LTP-BOPA: $sujet", $message, $headers);
            } else {
                $error_message = "Une erreur est survenue. Veuillez réessayer.";
            }
        } else {
            $error_message = implode('<br>', $errors);
        }
    }
}

logAction($pdo, 'contact', 'vue');
?>

<div class="contact-page">
    <div class="contact-header">
        <h1>📧 Contactez-nous</h1>
        <p class="subtitle">Une question ? Une suggestion ? N'hésitez pas à nous écrire</p>
    </div>
   
    <div class="contact-container">
        <div class="contact-info-side">
            <div class="info-card">
                <h3>📞 Coordonnées</h3>
                <ul>
                    <li>📍 LTP-BOPA, Bopa, Bénin</li>
                    <li>📞 +229 01 23 45 67</li>
                    <li>📧 contact@ltp-bopa.bj</li>
                    <li>🕒 Lun-Ven: 8h-17h</li>
                </ul>
            </div>
           
            <div class="info-card">
                <h3>💬 Autres moyens</h3>
                <ul>
                    <li><a href="index.php?page=messages">📢 Espace public</a></li>
                    <li><a href="index.php?page=faq">❓ FAQ</a></li>
                    <li><a href="index.php?page=guide">📖 Guide d'utilisation</a></li>
                </ul>
            </div>
           
            <div class="info-card">
                <h3>⏱️ Délais de réponse</h3>
                <p>Nous nous efforçons de répondre à tous les messages sous 24-48h ouvrées.</p>
            </div>
        </div>
       
        <div class="contact-form-side message-form-card">
            <?php if ($success_message): ?>
                <div class="alert success"><?= $success_message ?></div>
            <?php endif; ?>
           
            <?php if ($error_message): ?>
                <div class="alert error"><?= $error_message ?></div>
            <?php endif; ?>
           
            <form method="POST" class="contact-form message-form">
                <input type="hidden" name="action" value="envoyer_contact">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
               
                <div class="form-group">
                    <label for="nom">Nom complet *</label>
                    <input type="text" id="nom" name="nom" required
                           value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '' ?>">
                </div>
               
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
               
                <div class="form-group">
                    <label for="sujet">Sujet *</label>
                    <input type="text" id="sujet" name="sujet" required
                           value="<?= isset($_POST['sujet']) ? htmlspecialchars($_POST['sujet']) : '' ?>">
                </div>
               
                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea id="message" name="message" required rows="6"><?= isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '' ?></textarea>
                </div>
               
                <div class="form-group captcha">
                    <label for="captcha">Question de sécurité : 5 + 3 = ?</label>
                    <input type="number" id="captcha" name="captcha" required min="8" max="8">
                </div>
               
                <button type="submit" class="btn-submit btn-primary">Envoyer le message</button>
            </form>
        </div>
    </div>
   
    <!-- Carte -->
    <div class="map-container">
        <h3>📍 Nous trouver</h3>
        <div class="map-placeholder">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.986123456789!2d1.987654!3d6.54321!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMzInMzUuNiJOIDHCsDU5JzE1LjYiRQ!5e0!3m2!1sfr!2sbj!4v1234567890"
                width="100%"
                height="400"
                style="border:0;"
                allowfullscreen=""
                loading="lazy">
            </iframe>
        </div>
    </div>
</div>
