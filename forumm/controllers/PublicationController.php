<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/models/Publication.php';

class PublicationController {
    private $pdo;
    
    public function __construct() {
        $config = Config::getInstance();
        $this->pdo = $config->getPDO();
    }
    
    public function addPublication($publication) {
        try {
            $sql = "INSERT INTO publication (titre, contenu, categorie, tags, auteur, date_creation) 
                    VALUES (:titre, :contenu, :categorie, :tags, :auteur, NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':titre', $publication->getTitre(), PDO::PARAM_STR);
            $stmt->bindValue(':contenu', $publication->getContenu(), PDO::PARAM_STR);
            $stmt->bindValue(':categorie', $publication->getCategorie(), PDO::PARAM_STR);
            $stmt->bindValue(':tags', $publication->getTags(), PDO::PARAM_STR);
            $stmt->bindValue(':auteur', $publication->getAuteur(), PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                return $this->pdo->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur lors de l'ajout de la publication : " . $e->getMessage());
            return false;
        }
    }
    
    public function listPublications() {
        try {
            $sql = "SELECT * FROM publication ORDER BY date_creation DESC";
            $stmt = $this->pdo->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $publications = array();
            foreach ($results as $row) {
                $pub = new Publication();
                $pub->setIdPublication($row['id_publication']);
                $pub->setTitre($row['titre']);
                $pub->setContenu($row['contenu']);
                $pub->setCategorie($row['categorie']);
                $pub->setTags($row['tags']);
                $pub->setAuteur($row['auteur']);
                $pub->setDateCreation($row['date_creation']);
                $pub->setDateModification($row['date_modification']);
                $pub->setNombreLikes($row['nombre_likes']);
                $pub->setNombreCommentaires($row['nombre_commentaires']);
                $publications[] = $pub;
            }
            
            return $publications;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des publications : " . $e->getMessage());
            return false;
        }
    }
    
    public function getPublicationById($id_publication) {
        try {
            $sql = "SELECT * FROM publication WHERE id_publication = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id_publication, PDO::PARAM_INT);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $pub = new Publication();
                $pub->setIdPublication($row['id_publication']);
                $pub->setTitre($row['titre']);
                $pub->setContenu($row['contenu']);
                $pub->setCategorie($row['categorie']);
                $pub->setTags($row['tags']);
                $pub->setAuteur($row['auteur']);
                $pub->setDateCreation($row['date_creation']);
                $pub->setDateModification($row['date_modification']);
                $pub->setNombreLikes($row['nombre_likes']);
                $pub->setNombreCommentaires($row['nombre_commentaires']);
                return $pub;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de la publication : " . $e->getMessage());
            return false;
        }
    }
    
    public function updatePublication($publication) {
        try {
            $sql = "UPDATE publication SET 
                    titre = :titre, 
                    contenu = :contenu, 
                    categorie = :categorie, 
                    tags = :tags,
                    date_modification = NOW()
                    WHERE id_publication = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':titre', $publication->getTitre(), PDO::PARAM_STR);
            $stmt->bindValue(':contenu', $publication->getContenu(), PDO::PARAM_STR);
            $stmt->bindValue(':categorie', $publication->getCategorie(), PDO::PARAM_STR);
            $stmt->bindValue(':tags', $publication->getTags(), PDO::PARAM_STR);
            $stmt->bindValue(':id', $publication->getIdPublication(), PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de la publication : " . $e->getMessage());
            return false;
        }
    }
    
    public function deletePublication($id_publication) {
        try {
            $sql = "DELETE FROM publication WHERE id_publication = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id_publication, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression de la publication : " . $e->getMessage());
            return false;
        }
    }
    
    public function incrementLikes($id_publication) {
        try {
            $sql = "UPDATE publication SET nombre_likes = nombre_likes + 1 WHERE id_publication = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id_publication, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de l'incrémentation des likes : " . $e->getMessage());
            return false;
        }
    }
}
?>

