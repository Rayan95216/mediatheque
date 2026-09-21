-- Structure de la base "mediatheque" conforme au MPD du cahier des charges
-- + quelques données de test pour vérifier rapidement l'application.

CREATE DATABASE IF NOT EXISTS mediatheque CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mediatheque;

CREATE TABLE ADHERENT (
    id_adherent      INT AUTO_INCREMENT PRIMARY KEY,
    nom              VARCHAR(80) NOT NULL,
    prenom           VARCHAR(80) NOT NULL,
    email            VARCHAR(150) NOT NULL UNIQUE,
    date_inscription DATE NOT NULL
);

CREATE TABLE CATEGORIE (
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,
    libelle      VARCHAR(80) NOT NULL UNIQUE
);

CREATE TABLE AUTEUR (
    id_auteur INT AUTO_INCREMENT PRIMARY KEY,
    nom       VARCHAR(80) NOT NULL,
    prenom    VARCHAR(80) NOT NULL
);

CREATE TABLE LIVRE (
    id_livre          INT AUTO_INCREMENT PRIMARY KEY,
    titre             VARCHAR(200) NOT NULL,
    isbn              VARCHAR(20) NOT NULL UNIQUE,
    annee_publication INT,
    disponible        BOOLEAN NOT NULL DEFAULT TRUE,
    id_categorie      INT,
    FOREIGN KEY (id_categorie) REFERENCES CATEGORIE(id_categorie)
);

CREATE TABLE LIVRE_AUTEUR (
    id_livre  INT NOT NULL,
    id_auteur INT NOT NULL,
    PRIMARY KEY (id_livre, id_auteur),
    FOREIGN KEY (id_livre) REFERENCES LIVRE(id_livre) ON DELETE CASCADE,
    FOREIGN KEY (id_auteur) REFERENCES AUTEUR(id_auteur) ON DELETE CASCADE
);

CREATE TABLE EMPRUNT (
    id_emprunt         INT AUTO_INCREMENT PRIMARY KEY,
    id_adherent        INT NOT NULL,
    id_livre           INT NOT NULL,
    date_emprunt        DATE NOT NULL,
    date_retour_prevue  DATE NOT NULL,
    date_retour         DATE NULL,
    FOREIGN KEY (id_adherent) REFERENCES ADHERENT(id_adherent),
    FOREIGN KEY (id_livre) REFERENCES LIVRE(id_livre)
);

-- ---------- Données de test ----------

INSERT INTO CATEGORIE (libelle) VALUES
('Roman'), ('Science-fiction'), ('Informatique'), ('Bande dessinée');

INSERT INTO AUTEUR (nom, prenom) VALUES
('Camus', 'Albert'),
('Herbert', 'Frank'),
('Martin', 'Robert C.'),
('Uderzo', 'Albert');

INSERT INTO LIVRE (titre, isbn, annee_publication, disponible, id_categorie) VALUES
('L''Étranger', '9782070360024', 1942, TRUE, 1),
('Dune', '9782266311041', 1965, TRUE, 2),
('Clean Code', '9780132350884', 2008, TRUE, 3),
('Astérix le Gaulois', '9782012101333', 1961, TRUE, 4);

INSERT INTO LIVRE_AUTEUR (id_livre, id_auteur) VALUES
(1, 1), (2, 2), (3, 3), (4, 4);

INSERT INTO ADHERENT (nom, prenom, email, date_inscription) VALUES
('Dupont', 'Marie', 'marie.dupont@example.com', '2025-01-10'),
('Bernard', 'Lucas', 'lucas.bernard@example.com', '2025-03-22');

-- Exemple d'emprunt déjà en retard, pour tester F07
INSERT INTO EMPRUNT (id_adherent, id_livre, date_emprunt, date_retour_prevue, date_retour) VALUES
(1, 1, '2025-08-01', '2025-08-15', NULL);

UPDATE LIVRE SET disponible = FALSE WHERE id_livre = 1;
