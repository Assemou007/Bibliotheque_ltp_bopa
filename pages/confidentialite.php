<?php
// pages/confidentialite.php

logAction($pdo, 'confidentialite', 'vue');
?>

<div class="privacy-page">
    <div class="privacy-header">
        <h1>🔒 Politique de confidentialité</h1>
        <p class="subtitle">Comment nous protégeons vos données personnelles</p>
    </div>
   
    <div class="privacy-content">
        <div class="privacy-section">
            <h2>1. Introduction</h2>
            <p>
                La présente politique de confidentialité décrit comment le LTP-BOPA collecte,
                utilise et protège les informations que vous nous fournissez lorsque vous utilisez
                la bibliothèque numérique.
            </p>
        </div>
       
        <div class="privacy-section">
            <h2>2. Données collectées</h2>
            <p>Nous collectons uniquement les données nécessaires au fonctionnement du site :</p>
            <ul>
                <li>Nom ou pseudo (pour les messages publics)</li>
                <li>Adresse email (optionnelle, pour réponse)</li>
                <li>Adresse IP (pour des raisons de sécurité)</li>
                <li>Contenu des messages que vous publiez</li>
                <li>Données de navigation anonymes (pages visitées)</li>
            </ul>
            <p>Aucune donnée bancaire ou sensible n'est collectée.</p>
        </div>
       
        <div class="privacy-section">
            <h2>3. Utilisation des données</h2>
            <p>Vos données sont utilisées pour :</p>
            <ul>
                <li>Permettre la publication de messages publics</li>
                <li>Répondre à vos questions via le formulaire de contact</li>
                <li>Améliorer le site (statistiques anonymes)</li>
                <li>Assurer la sécurité du site (logs d'accès)</li>
            </ul>
        </div>
       
        <div class="privacy-section">
            <h2>4. Conservation des données</h2>
            <p>
                Les messages publics sont conservés indéfiniment dans l'intérêt de la communauté.
                Les logs d'accès sont conservés 1 an.
                Les données de contact sont conservées 3 ans.
            </p>
        </div>
       
        <div class="privacy-section">
            <h2>5. Partage des données</h2>
            <p>
                Vos données ne sont jamais vendues ou louées à des tiers. Elles peuvent être
                communiquées aux autorités compétentes en cas d'obligation légale.
            </p>
        </div>
       
        <div class="privacy-section">
            <h2>6. Vos droits</h2>
            <p>Conformément au RGPD, vous avez les droits suivants :</p>
            <ul>
                <li>Droit d'accès : savoir quelles données nous détenons</li>
                <li>Droit de rectification : corriger des données inexactes</li>
                <li>Droit à l'effacement : demander la suppression de vos données</li>
                <li>Droit d'opposition : vous opposer au traitement</li>
            </ul>
            <p>Pour exercer vos droits, contactez-nous à : dpo@ltp-bopa.bj</p>
        </div>
       
        <div class="privacy-section">
            <h2>7. Sécurité</h2>
            <p>
                Nous mettons en œuvre des mesures techniques et organisationnelles appropriées
                pour protéger vos données contre tout accès non autorisé, modification,
                divulgation ou destruction.
            </p>
        </div>
       
        <div class="privacy-section">
            <h2>8. Contact</h2>
            <p>
                Pour toute question relative à cette politique, vous pouvez nous écrire à :<br>
                Email : dpo@ltp-bopa.bj<br>
                Courrier : LTP-BOPA, Bopa, Bénin (à l'attention du DPO)
            </p>
        </div>
       
        <div class="privacy-footer">
            <p>Dernière mise à jour : 1er mars 2026</p>
        </div>
    </div>
</div>
