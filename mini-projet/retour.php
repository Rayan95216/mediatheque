<?php
require 'config/db.php';

$message = null;
$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idEmprunt = $_POST['id_emprunt'] ?? '';

    if ($idEmprunt === '') {
        $erreur = "Emprunt introuvable.";
    } else {
        // Récupère le livre concerné par cet emprunt
        $check = $pdo->prepare("SELECT id_livre, date_retour FROM EMPRUNT WHERE id_emprunt = :id");
        $check->execute(['id' => $idEmprunt]);
        $emprunt = $check->fetch();

        if (!$emprunt) {
            $erreur = "Emprunt introuvable.";
        } elseif ($emprunt['date_retour'] !== null) {
            $erreur = "Ce livre a déjà été rendu.";
        } else {
            $pdo->beginTransaction();
            try {
                // 1. renseigner date_retour avec la date du jour
                $update1 = $pdo->prepare("UPDATE EMPRUNT SET date_retour = :date_retour WHERE id_emprunt = :id");
                $update1->execute([
                    'date_retour' => date('Y-m-d'),
                    'id' => $idEmprunt,
                ]);

                // 2. passer le livre concerné à disponible = TRUE
                $update2 = $pdo->prepare("UPDATE LIVRE SET disponible = 1 WHERE id_livre = :id_livre");
                $update2->execute(['id_livre' => $emprunt['id_livre']]);

                $pdo->commit();
                $message = "Retour enregistré avec succès.";
            } catch (Exception $e) {
                $pdo->rollBack();
                $erreur = "Erreur lors de l'enregistrement du retour.";
            }
        }
    }
}

require 'includes/header.php';
?>

<div class="card" style="max-width:520px;">
    <h2>Retour d'un livre</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if ($erreur): ?>
        <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <a class="btn" href="emprunts.php">← Retour à la liste des emprunts en cours</a>
</div>

<?php require 'includes/footer.php'; ?>
