<?php
require 'config/db.php';

$message = null;
$erreur = null;

// valeurs à réafficher si erreur
$titre = '';
$auteurs = '';
$idCategorie = '';
$nouvelleCategorie = '';
$annee = '';
$isbn = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $auteurs = trim($_POST['auteurs'] ?? '');
    $idCategorie = $_POST['id_categorie'] ?? '';
    $nouvelleCategorie = trim($_POST['nouvelle_categorie'] ?? '');
    $annee = trim($_POST['annee'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');

    if ($titre === '' || $isbn === '' || $annee === '') {
        $erreur = "Merci de remplir au minimum le titre, l'année et l'ISBN.";
    } elseif ($idCategorie === '' && $nouvelleCategorie === '') {
        $erreur = "Merci de choisir une catégorie ou d'en saisir une nouvelle.";
    } else {
        $pdo->beginTransaction();
        try {
            // 1. Catégorie : nouvelle catégorie si saisie, sinon celle sélectionnée
            if ($nouvelleCategorie !== '') {
                $stmtCat = $pdo->prepare("SELECT id_categorie FROM CATEGORIE WHERE libelle = :libelle");
                $stmtCat->execute(['libelle' => $nouvelleCategorie]);
                $cat = $stmtCat->fetch();

                if ($cat) {
                    $idCategorieFinal = $cat['id_categorie'];
                } else {
                    $insertCat = $pdo->prepare("INSERT INTO CATEGORIE (libelle) VALUES (:libelle)");
                    $insertCat->execute(['libelle' => $nouvelleCategorie]);
                    $idCategorieFinal = $pdo->lastInsertId();
                }
            } else {
                $idCategorieFinal = $idCategorie;
            }

            // 2. Le livre
            $insertLivre = $pdo->prepare("
                INSERT INTO LIVRE (titre, isbn, annee_publication, disponible, id_categorie)
                VALUES (:titre, :isbn, :annee, 1, :id_categorie)
            ");
            $insertLivre->execute([
                'titre' => $titre,
                'isbn' => $isbn,
                'annee' => $annee,
                'id_categorie' => $idCategorieFinal,
            ]);
            $idLivre = $pdo->lastInsertId();

            // 3. Les auteurs : saisis séparés par des virgules, ex. "Albert Camus, Frank Herbert"
            $nomsAuteurs = array_filter(array_map('trim', explode(',', $auteurs)));

            foreach ($nomsAuteurs as $nomComplet) {
                $parties = preg_split('/\s+/', $nomComplet);
                $nomAuteur = array_pop($parties);           // dernier mot = nom
                $prenomAuteur = implode(' ', $parties);       // reste = prénom

                $stmtAuteur = $pdo->prepare("
                    SELECT id_auteur FROM AUTEUR WHERE nom = :nom AND prenom = :prenom
                ");
                $stmtAuteur->execute(['nom' => $nomAuteur, 'prenom' => $prenomAuteur]);
                $auteur = $stmtAuteur->fetch();

                if ($auteur) {
                    $idAuteur = $auteur['id_auteur'];
                } else {
                    $insertAuteur = $pdo->prepare("INSERT INTO AUTEUR (nom, prenom) VALUES (:nom, :prenom)");
                    $insertAuteur->execute(['nom' => $nomAuteur, 'prenom' => $prenomAuteur]);
                    $idAuteur = $pdo->lastInsertId();
                }

                $insertLA = $pdo->prepare("
                    INSERT INTO LIVRE_AUTEUR (id_livre, id_auteur) VALUES (:id_livre, :id_auteur)
                ");
                $insertLA->execute(['id_livre' => $idLivre, 'id_auteur' => $idAuteur]);
            }

            $pdo->commit();
            $message = "Livre \"$titre\" ajouté avec succès.";
            // on vide le formulaire après succès
            $titre = $auteurs = $isbn = $annee = $nouvelleCategorie = '';
            $idCategorie = '';
        } catch (PDOException $e) {
            $pdo->rollBack();
            if ($e->getCode() === '23000') {
                $erreur = "Cet ISBN est déjà utilisé par un autre livre.";
            } else {
                $erreur = "Erreur lors de l'ajout du livre.";
            }
        }
    }
}

$categories = $pdo->query("SELECT id_categorie, libelle FROM CATEGORIE ORDER BY libelle")->fetchAll();

require 'includes/header.php';
?>

<div class="card" style="max-width:560px;">
    <h2>Ajouter un livre</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if ($erreur): ?>
        <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="post" action="ajouter_livre.php">
        <label for="titre">Titre</label>
        <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($titre) ?>" required>

        <label for="auteurs">Auteur(s)</label>
        <input type="text" id="auteurs" name="auteurs" value="<?= htmlspecialchars($auteurs) ?>" placeholder="Ex : Albert Camus, Frank Herbert">
        <small style="color:#6b7280;">Séparez plusieurs auteurs par une virgule.</small>

        <label for="id_categorie">Catégorie</label>
        <select id="id_categorie" name="id_categorie">
            <option value="">-- Choisir une catégorie existante --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id_categorie'] ?>" <?= $idCategorie == $cat['id_categorie'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['libelle']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="nouvelle_categorie">... ou nouvelle catégorie</label>
        <input type="text" id="nouvelle_categorie" name="nouvelle_categorie" value="<?= htmlspecialchars($nouvelleCategorie) ?>" placeholder="Laisser vide si catégorie déjà choisie ci-dessus">

        <label for="annee">Année de publication</label>
        <input type="text" id="annee" name="annee" value="<?= htmlspecialchars($annee) ?>" required>

        <label for="isbn">ISBN</label>
        <input type="text" id="isbn" name="isbn" value="<?= htmlspecialchars($isbn) ?>" required>

        <button type="submit">Ajouter le livre</button>
    </form>

    <p style="margin-top:16px;">
        <a href="livres.php">← Retour à la liste des livres</a>
    </p>
</div>

<?php require 'includes/footer.php'; ?>
