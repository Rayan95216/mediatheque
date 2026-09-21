<?php
require 'config/db.php';

$adherents = $pdo->query("
    SELECT id_adherent, nom, prenom, email, date_inscription
    FROM ADHERENT
    ORDER BY nom, prenom
")->fetchAll();

require 'includes/header.php';
?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <h2>Adhérents (<?= count($adherents) ?>)</h2>
        <a class="btn" href="ajouter_adherent.php" style="margin-top:0;">+ Ajouter un adhérent</a>
    </div>
    <table>
        <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>E-mail</th>
            <th>Date d'inscription</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($adherents)): ?>
            <tr><td colspan="4">Aucun adhérent enregistré.</td></tr>
        <?php endif; ?>
        <?php foreach ($adherents as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['nom']) ?></td>
                <td><?= htmlspecialchars($a['prenom']) ?></td>
                <td><?= htmlspecialchars($a['email']) ?></td>
                <td><?= htmlspecialchars(date('d/m/Y', strtotime($a['date_inscription']))) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require 'includes/footer.php'; ?>
