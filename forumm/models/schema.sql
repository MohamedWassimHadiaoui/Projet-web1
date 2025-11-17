CREATE DATABASE IF NOT EXISTS forumm CHARACTER SET utf8 COLLATE utf8_general_ci;

USE forumm;

DROP TABLE IF EXISTS commentaire;
DROP TABLE IF EXISTS publication;

CREATE TABLE publication (
    id_publication INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    categorie VARCHAR(50) NOT NULL,
    tags VARCHAR(255) DEFAULT NULL,
    auteur VARCHAR(100) NOT NULL,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT NULL,
    nombre_likes INT DEFAULT 0,
    nombre_commentaires INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE commentaire (
    id_commentaire INT AUTO_INCREMENT PRIMARY KEY,
    id_publication INT NOT NULL,
    contenu TEXT NOT NULL,
    auteur VARCHAR(100) NOT NULL,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT NULL,
    nombre_likes INT DEFAULT 0,
    FOREIGN KEY (id_publication) REFERENCES publication(id_publication) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX idx_publication_categorie ON publication(categorie);
CREATE INDEX idx_publication_date ON publication(date_creation);
CREATE INDEX idx_commentaire_publication ON commentaire(id_publication);

INSERT INTO publication (titre, contenu, categorie, tags, auteur) VALUES
('Bienvenue sur le forum', 'Ceci est un message de bienvenue pour tester le forum. N''hésitez pas à créer vos propres publications !', 'discussion', 'bienvenue,forum', 'Administrateur'),
('Besoin de conseils', 'Bonjour à tous, j''ai besoin de conseils pour signaler un incident. Merci pour votre aide.', 'support', 'conseils,soutien', 'Sophie Martin'),
('Mon expérience positive', 'Je voulais partager mon expérience positive avec la médiation communautaire. C''était vraiment utile !', 'experience', 'médiation,expérience', 'Jean Dubois');

INSERT INTO commentaire (id_publication, contenu, auteur) VALUES
(1, 'Merci pour ce message de bienvenue !', 'Utilisateur Test'),
(2, 'Je peux vous aider avec cela. Contactez-moi en privé.', 'Marie Dupont'),
(2, 'Très bonne question, j''ai vécu une situation similaire.', 'Pierre Laurent');

