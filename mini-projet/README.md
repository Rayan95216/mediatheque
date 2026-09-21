# Médiathèque — Mini-projet BTS CIEL IR

Application Web (HTML/CSS/PHP/PDO/MySQL) permettant au personnel d'une médiathèque
de gérer les livres, les adhérents, les emprunts et les retours, sans passer par phpMyAdmin.

## Membres du binôme
- ... (à compléter)
- ... (à compléter)

## Installation

1. Copier le dossier `mediatheque/` dans `htdocs/` (XAMPP).
2. Importer `mediatheque.sql` dans MariaDB (via phpMyAdmin ou en ligne de commande) :
   `mysql -u root -p < mediatheque.sql`
3. Vérifier les identifiants de connexion dans `config/db.php` (par défaut : `root` / mot de passe vide).
4. Démarrer Apache + MySQL, puis ouvrir `http://localhost/mediatheque/`.

## Fonctionnalités terminées

- F01 — Accueil avec menu de navigation commun + mini tableau de bord
- F02 — Liste des livres (titre, auteur(s), catégorie, année, ISBN, disponibilité)
- F03 — Recherche par titre, avec filtre par catégorie (bonus)
- F04 — Liste des adhérents
- F05 — Nouvel emprunt (adhérent + livre disponible, retour prévu à J+14)
- F06 — Emprunts en cours
- F07 — Mise en évidence des retards ("EN RETARD")
- F08 — Retour d'un livre (mise à jour de la disponibilité)

## Fonctionnalités bonus ajoutées

- Ajout d'un adhérent depuis l'application (`ajouter_adherent.php`)
- Ajout d'un livre depuis l'application, avec gestion des auteurs et des catégories (`ajouter_livre.php`)
- Tableau de bord sur l'accueil (nombre de livres, d'adhérents, d'emprunts en cours et de retards)

## Fonctionnalités non terminées / à finaliser

- Modification d'un livre ou d'un adhérent existant
- Pagination de la liste des livres (bonus)
- Captures d'écran et export SQL final de la base à joindre à la remise

## Sécurité

- Connexion à la base via PDO, requêtes préparées partout (aucune concaténation SQL).
- Toutes les données affichées provenant de la base ou de l'utilisateur passent par `htmlspecialchars()`.

## Difficultés rencontrées

- ... (à compléter par le binôme)
