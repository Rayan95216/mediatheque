<?php
// Récupère le nom de la page courante pour surligner le lien actif dans le menu
$page_courante = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Médiathèque</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header>
    <div class="header-inner">
        <h1>📚 Médiathèque</h1>
        <nav>
            <ul>
                <li><a href="index.php" class="<?= $page_courante === 'index.php' ? 'active' : '' ?>">Accueil</a></li>
                <li><a href="livres.php" class="<?= $page_courante === 'livres.php' ? 'active' : '' ?>">Livres</a></li>
                <li><a href="adherents.php" class="<?= $page_courante === 'adherents.php' ? 'active' : '' ?>">Adhérents</a></li>
                <li><a href="emprunts.php" class="<?= $page_courante === 'emprunts.php' ? 'active' : '' ?>">Emprunts en cours</a></li>
                <li><a href="emprunter.php" class="<?= $page_courante === 'emprunter.php' ? 'active' : '' ?>">Nouvel emprunt</a></li>
            </ul>
        </nav>
    </div>
</header>
<main class="container">
