<?php
require 'config/db.php';

$emprunts = $pdo->query("
    SELECT
        e.id_emprunt, e.date_emprunt, e.date_retour_prevue,
        CONCAT(ad.prenom, ' ', ad.nom) AS adherent,
        l.titre AS livre,
        (e.date_retour_prevue < CURDATE()) AS en_retard
    FROM EMPRUNT e
    JOIN ADHERENT ad ON ad.id_adherent = e.id_adherent
    JOIN LIVRE l ON l.id_livre = e.id_livre
    WHERE e.date_retour IS NULL
    ORDER BY e.date_retour_prevue ASC
")->fetchAll();

require 'includes/header.php';
?>

<div class="card">
    <h2>Emprunts en cours (<?= count($emprunts) ?>)</h2>
    <table>
        <thead>
        <tr>
            <th>Adhérent</th>
            <th>Livre</th>
            <th>Date d'emprunt</th>
            <th>Retour prévu</th>
            <th>Statut</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($emprunts)): ?>
            <tr><td colspan="6">Aucun emprunt en cours.</td></tr>
        <?php endif; ?>
        <?php foreach ($emprunts as $e): ?>
            <tr class="<?= $e['en_retard'] ? 'retard' : '' ?>">
                <td><?= htmlspecialchars($e['adherent']) ?></td>
                <td><?= htmlspecialchars($e['livre']) ?></td>
                <td><?= htmlspecialchars(date('d/m/Y', strtotime($e['date_emprunt']))) ?></td>
                <td><?= htmlspecialchars(date('d/m/Y', strtotime($e['date_retour_prevue']))) ?></td>
                <td>
                    <?php if ($e['en_retard']): ?>
                        <span class="badge badge-retard">EN RETARD</span>
                    <?php else: ?>
                        <span class="badge badge-emprunte">En cours</span>
                    <?php endif; ?>
                </td>
                <td>
                    <form method="post" action="retour.php" style="margin:0;">
                        <input type="hidden" name="id_emprunt" value="<?= $e['id_emprunt'] ?>">
                        <button type="submit" class="btn-retour">Marquer comme rendu</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require 'includes/footer.php'; ?>
