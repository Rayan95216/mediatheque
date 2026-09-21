<?php
require 'config/db.php';

$nbLivres = $pdo->query("SELECT COUNT(*) FROM LIVRE")->fetchColumn();
$nbAdherents = $pdo->query("SELECT COUNT(*) FROM ADHERENT")->fetchColumn();
$nbEmpruntsEnCours = $pdo->query("SELECT COUNT(*) FROM EMPRUNT WHERE date_retour IS NULL")->fetchColumn();
$nbRetards = $pdo->query("
    SELECT COUNT(*) FROM EMPRUNT
    WHERE date_retour IS NULL AND date_retour_prevue < CURDATE()
")->fetchColumn();

require 'includes/header.php';
?>

<div class="card">
    <h2>Bienvenue</h2>
    <p>Application de gestion de la médiathèque : consultez les livres et les adhérents, enregistrez les emprunts et les retours, et repérez en un coup d'œil les retards.</p>
</div>

<div class="stats">
    <div class="stat-box">
        <div class="nb"><?= (int) $nbLivres ?></div>
        <div class="label">Livres</div>
    </div>
    <div class="stat-box">
        <div class="nb"><?= (int) $nbAdherents ?></div>
        <div class="label">Adhérents</div>
    </div>
    <div class="stat-box">
        <div class="nb"><?= (int) $nbEmpruntsEnCours ?></div>
        <div class="label">Emprunts en cours</div>
    </div>
    <div class="stat-box">
        <div class="nb" style="color:#b3261e"><?= (int) $nbRetards ?></div>
        <div class="label">En retard</div>
    </div>
</div>

<div class="card">
    <h2>Accès rapide</h2>
    <a class="btn" href="livres.php">Voir les livres</a>
    <a class="btn" href="emprunter.php">Nouvel emprunt</a>
    <a class="btn" href="emprunts.php">Emprunts en cours</a>
</div>

<?php require 'includes/footer.php'; ?>
