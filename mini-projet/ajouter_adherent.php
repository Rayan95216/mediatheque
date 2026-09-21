<?php
require 'config/db.php';

$message = null;
$erreur = null;

// valeurs à réafficher si erreur
$nom = '';
$prenom = '';
$email = '';
$dateInscription = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $dateInscription = $_POST['date_inscription'] ?? date('Y-m-d');

    if ($nom === '' || $prenom === '' || $email === '') {
        $erreur = "Merci de remplir tous les champs.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse e-mail n'est pas valide.";
    } else {
        try {
            $insert = $pdo->prepare("
                INSERT INTO ADHERENT (nom, prenom, email, date_inscription)
                VALUES (:nom, :prenom, :email, :date_inscription)
            ");
            $insert->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'date_inscription' => $dateInscription,
            ]);

            $message = "Adhérent \"$prenom $nom\" ajouté avec succès.";
            // on vide le formulaire après succès
            $nom = $prenom = $email = '';
            $dateInscription = date('Y-m-d');
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $erreur = "Cet e-mail est déjà utilisé par un autre adhérent.";
            } else {
                $erreur = "Erreur lors de l'ajout de l'adhérent.";
            }
        }
    }
}

require 'includes/header.php';
?>

<div class="card" style="max-width:520px;">
    <h2>Ajouter un adhérent</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if ($erreur): ?>
        <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="post" action="ajouter_adherent.php">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>" required>

        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($prenom) ?>" required>

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

        <label for="date_inscription">Date d'inscription</label>
        <input type="date" id="date_inscription" name="date_inscription" value="<?= htmlspecialchars($dateInscription) ?>" required>

        <button type="submit">Ajouter l'adhérent</button>
    </form>

    <p style="margin-top:16px;">
        <a href="adherents.php">← Retour à la liste des adhérents</a>
    </p>
</div>

<?php require 'includes/footer.php'; ?>
