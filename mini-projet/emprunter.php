<?php
require 'config/db.php';

$message = null;
$erreur = null;

// --- Traitement du formulaire ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idAdherent = $_POST['id_adherent'] ?? '';
    $idLivre = $_POST['id_livre'] ?? '';

    if ($idAdherent === '' || $idLivre === '') {
        $erreur = "Veuillez sélectionner un adhérent et un livre.";
    } else {
        // Sécurité : on revérifie que le livre est bien disponible avant d'emprunter
        $check = $pdo->prepare("SELECT disponible FROM LIVRE WHERE id_livre = :id");
        $check->execute(['id' => $idLivre]);
        $livre = $check->fetch();

        if (!$livre) {
            $erreur = "Ce livre n'existe pas.";
        } elseif (!$livre['disponible']) {
            $erreur = "Ce livre n'est plus disponible.";
        } else {
            $dateEmprunt = date('Y-m-d');
            $dateRetourPrevue = date('Y-m-d', strtotime('+14 days'));

            $pdo->beginTransaction();
            try {
                // 1. insérer une ligne dans EMPRUNT
                $insert = $pdo->prepare("
                    INSERT INTO EMPRUNT (id_adherent, id_livre, date_emprunt, date_retour_prevue, date_retour)
                    VALUES (:id_adherent, :id_livre, :date_emprunt, :date_retour_prevue, NULL)
                ");
                $insert->execute([
                    'id_adherent' => $idAdherent,
                    'id_livre' => $idLivre,
                    'date_emprunt' => $dateEmprunt,
                    'date_retour_prevue' => $dateRetourPrevue,
                ]);

                // 2. passer le livre à disponible = FALSE
                $update = $pdo->prepare("UPDATE LIVRE SET disponible = 0 WHERE id_livre = :id");
                $update->execute(['id' => $idLivre]);

                $pdo->commit();
                $message = "Emprunt enregistré. Retour prévu le " . date('d/m/Y', strtotime($dateRetourPrevue)) . ".";
            } catch (Exception $e) {
                $pdo->rollBack();
                $erreur = "Erreur lors de l'enregistrement de l'emprunt.";
            }
        }
    }
}

// --- Données pour le formulaire ---
$adherents = $pdo->query("SELECT id_adherent, nom, prenom FROM ADHERENT ORDER BY nom, prenom")->fetchAll();

// uniquement les livres disponibles
$livresDisponibles = $pdo->query("
    SELECT id_livre, titre
    FROM LIVRE
    WHERE disponible = 1
    ORDER BY titre
")->fetchAll();

require 'includes/header.php';
?>

<div class="card" style="max-width:520px;">
    <h2>Nouvel emprunt</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if ($erreur): ?>
        <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="post" action="emprunter.php">
        <label for="id_adherent">Adhérent</label>
        <select id="id_adherent" name="id_adherent" required>
            <option value="">-- Choisir un adhérent --</option>
            <?php foreach ($adherents as $a): ?>
                <option value="<?= $a['id_adherent'] ?>">
                    <?= htmlspecialchars($a['nom'] . ' ' . $a['prenom']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="id_livre">Livre disponible</label>
        <select id="id_livre" name="id_livre" required>
            <option value="">-- Choisir un livre --</option>
            <?php foreach ($livresDisponibles as $l): ?>
                <option value="<?= $l['id_livre'] ?>">
                    <?= htmlspecialchars($l['titre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <p style="margin-top:12px; color:#6b7280; font-size:0.9rem;">
            Date d'emprunt : aujourd'hui — Retour prévu : dans 14 jours.
        </p>

        <button type="submit">Enregistrer l'emprunt</button>
    </form>
</div>

<?php require 'includes/footer.php'; ?>
