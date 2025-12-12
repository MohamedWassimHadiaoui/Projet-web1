-- Migration: Ajout de la colonne 'statut' pour la modération des publications
-- Exécuter ce script dans phpMyAdmin pour mettre à jour la base de données existante

USE forumm;

-- Ajouter la colonne statut si elle n'existe pas
ALTER TABLE publication 
ADD COLUMN IF NOT EXISTS statut ENUM('pending', 'approved', 'rejected') DEFAULT 'pending';

-- Mettre à jour les publications existantes comme approuvées
UPDATE publication SET statut = 'approved' WHERE statut IS NULL OR statut = 'pending';

-- Vérification
SELECT id_publication, titre, statut FROM publication;


