-- db.sql
-- Example SQL to create events table for TasnimCRUD

CREATE DATABASE IF NOT EXISTS tasnimcrud CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE tasnimcrud;

CREATE TABLE IF NOT EXISTS events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  date_event DATETIME,
  type VARCHAR(50),
  location VARCHAR(255),
  participants INT DEFAULT 0,
  tags VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data for testing
INSERT IGNORE INTO events (id, title, description, date_event, type, location, participants, tags) VALUES
(1, 'Atelier: Médiation et résolution de conflits', 'Apprenez les techniques de base de la médiation et découvrez comment résoudre les conflits de manière pacifique dans votre communauté. Animé par des médiateurs expérimentés.', '2024-03-15 18:00', 'online', 'Zoom', 25, '#médiation, #formation, #paix'),
(2, 'Conférence: L\'inclusion dans nos quartiers', 'Une conférence interactive sur les moyens de promouvoir l\'inclusion et la diversité dans nos communautés locales.', '2024-03-22 14:00', 'offline', 'Paris, Centre communautaire', 45, '#inclusion, #communauté, #diversité'),
(3, 'Formation: Prévention de la violence', 'Formation complète sur la prévention de la violence, la reconnaissance des signes avant-coureurs et les actions à entreprendre.', '2024-03-30 10:00', 'hybrid', 'Lyon + Zoom', 60, '#prévention, #formation, #sécurité'),
(4, 'Rencontre: Partage d\'expériences', 'Une soirée conviviale pour partager vos expériences, vos réussites et vos défis dans la promotion de la paix.', '2024-04-05 19:00', 'online', 'Zoom', 18, '#rencontre, #partage, #communauté'),
(5, 'Atelier: Communication non-violente', 'Découvrez les principes de la communication non-violente pour améliorer vos relations et résoudre les conflits.', '2024-04-12 16:00', 'offline', 'Marseille, Salle communautaire', 30, '#communication, #atelier, #paix'),
(6, 'Webinaire: Droits et recours', 'Un webinaire informatif sur vos droits en cas de discrimination, harcèlement ou violence, et les recours disponibles.', '2024-04-20 20:00', 'online', 'Plateforme web', 52, '#droits, #juridique, #protection');

-- Table pour l'entité Contenus (articles, posts, ressources)
CREATE TABLE IF NOT EXISTS contenus (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  body TEXT,
  author VARCHAR(255) DEFAULT NULL,
  status VARCHAR(50) DEFAULT 'draft',
  likes INT DEFAULT 0,
  tags VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO contenus (id, title, body, author, status, tags, likes) VALUES
(1, 'Guide de la médiation communautaire', 'Ce guide présente les étapes clés pour mener une médiation locale efficace.', 'Admin', 'published', '#médiation, #guide', 0),
(2, 'Ressources: Communication non-violente', 'Liste de ressources et exercices pour pratiquer la communication non-violente au quotidien.', 'Redaction', 'published', '#communication, #ressources', 12),
(3, 'Comment signaler une situation', 'Procédure détaillée pour signaler une situation problématique aux autorités compétentes.', 'Equipe', 'draft', '#signalement, #sécurité', 1);
