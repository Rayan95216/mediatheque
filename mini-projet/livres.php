<?php
require 'config/db.php';

// --- Recherche (F03) : par titre, bonus par auteur ou catégorie ---
$recherche = trim($_GET['q'] ?? '');
$categorieId = $_GET['categorie'] ?? '';

$sql = "
    SELECT
        l.id_livre, l.titre, l.isbn, l.annee_publication, l.disponible,
        c.libelle AS categorie,
        GROUP_CONCAT(DISTINCT CONCAT(a.prenom, ' ', a.nom) SEPARATOR ', ') AS auteurs
    FROM LIVRE l
    LEFT JOIN CATEGORIE c ON c.id_categorie = l.id_categorie
    LEFT JOIN LIVRE_AUTEUR la ON la.id_livre = l.id_livre
    LEFT JOIN AUTEUR a ON a.id_auteur = la.id_auteur
    WHERE 1 = 1
";
$params = [];

if ($recherche !== '') {
    // recherche par titre, ou (bonus) par nom/prénom d'auteur
    $sql .= " AND (l.titre LIKE :q OR a.nom LIKE :q OR a.prenom LIKE :q)";
    $params['q'] = '%' . $recherche . '%';
}

if ($categorieId !== '') {
    $sql .= " AND l.id_categorie = :categorie";
    $params['categorie'] = $categorieId;
}

$sql .= " GROUP BY l.id_livre ORDER BY l.titre ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$livres = $stmt->fetchAll();

$categories = $pdo->query("SELECT id_categorie, libelle FROM CATEGORIE ORDER BY libelle")->fetchAll();

require 'includes/header.php';
?>

<div class="card">
    <h2>Rechercher un livre</h2>
    <form method="get" action="livres.php" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
        <div style="flex:1; min-width:200px;">
            <label for="q">Titre ou auteur</label>
            <input type="text" id="q" name="q" value="<?= htmlspecialchars($recherche) ?>" placeholder="Ex : Camus, L'Étranger...">
        </div>
        <div style="min-width:180px;">
            <label for="categorie">Catégorie</label>
            <select id="categorie" name="categorie">
                <option value="">Toutes</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id_categorie'] ?>" <?= $categorieId == $cat['id_categorie'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <button type="submit">Rechercher</button>
        </div>
    </form>
</div>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <h2>Livres (<?= count($livres) ?>)</h2>
        <a class="btn" href="ajouter_livre.php" style="margin-top:0;">+ Ajouter un livre</a>
    </div>
    <table>
        <thead>
        <tr>
            <th>Titre</th>
            <th>Auteur(s)</th>
            <th>Catégorie</th>
            <th>Année</th>
            <th>ISBN</th>
            <th>Disponibilité</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($livres)): ?>
            <tr><td colspan="6">Aucun livre trouvé.</td></tr>
        <?php endif; ?>
        <?php foreach ($livres as $livre): ?>
            <tr>
                <td><?= htmlspecialchars($livre['titre']) ?></td>
                <td><?= htmlspecialchars($livre['auteurs'] ?? '—') ?></td>
                <td><?= htmlspecialchars($livre['categorie'] ?? '—') ?></td>
                <td><?= htmlspecialchars($livre['annee_publication']) ?></td>
                <td><?= htmlspecialchars($livre['isbn']) ?></td>
                <td>
                    <?php if ($livre['disponible']): ?>
                        <span class="badge badge-dispo">Disponible</span>
                    <?php else: ?>
                        <span class="badge badge-emprunte">Emprunté</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require 'includes/footer.php'; ?>
