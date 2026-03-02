<?php
// pages/maintenance.php
http_response_code(503);
?>

<div class="maintenance-page">
    <div class="maintenance-content">
        <div class="maintenance-icon">🔧</div>
        <h1>Site en maintenance</h1>
        <p class="maintenance-message">
            La bibliothèque numérique est actuellement en maintenance pour quelques instants.
            Merci de votre compréhension.
        </p>
        <div class="maintenance-timer">
            <p>Tentative de reconnexion automatique dans :</p>
            <div class="countdown">30 secondes</div>
        </div>
        <div class="maintenance-contact">
            <p>Pour toute urgence : <a href="mailto:contact@ltp-bopa.bj">contact@ltp-bopa.bj</a></p>
        </div>
    </div>
</div>

<script>
let seconds = 30;
const countdownEl = document.querySelector('.countdown');
const interval = setInterval(() => {
    seconds--;
    if (seconds <= 0) {
        clearInterval(interval);
        location.reload();
    } else {
        countdownEl.textContent = seconds + ' secondes';
    }
}, 1000);
</script>