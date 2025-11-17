<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/models/Commentaire.php';

class CommentaireController {
    private $pdo;
    
    public function __construct() {
        $config = Config::getInstance();
        $this->pdo = $config->getPDO();
    }
    
    public function addCommentaire($commentaire) {
        try {
            $this->pdo->beginTransaction();
            
            $sql = "INSERT INTO commentaire (id_publication, contenu, auteur, date_creation) 
                    VALUES (:id_publication, :contenu, :auteur, NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_publication', $commentaire->getIdPublication(), PDO::PARAM_INT);
            $stmt->bindValue(':contenu', $commentaire->getContenu(), PDO::PARAM_STR);
            $stmt->bindValue(':auteur', $commentaire->getAuteur(), PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                $id_commentaire = $this->pdo->lastInsertId();
                
                $sqlUpdate = "UPDATE publication SET nombre_commentaires = nombre_commentaires + 1 
                             WHERE id_publication = :id";
                $stmtUpdate = $this->pdo->prepare($sqlUpdate);
                $stmtUpdate->bindValue(':id', $commentaire->getIdPublication(), PDO::PARAM_INT);
                $stmtUpdate->execute();
                
                $this->pdo->commit();
                return $id_commentaire;
            }
            
            $this->pdo->rollBack();
            return false;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur lors de l'ajout du commentaire : " . $e->getMessage());
            return false;
        }
    }
    
    public function listCommentairesByPublication($id_publication) {
        try {
            $sql = "SELECT * FROM commentaire WHERE id_publication = :id ORDER BY date_creation ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id_publication, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $commentaires = array();
            foreach ($results as $row) {
                $com = new Commentaire();
                $com->setIdCommentaire($row['id_commentaire']);
                $com->setIdPublication($row['id_publication']);
                $com->setContenu($row['contenu']);
                $com->setAuteur($row['auteur']);
                $com->setDateCreation($row['date_creation']);
                $com->setDateModification($row['date_modification']);
                $com->setNombreLikes($row['nombre_likes']);
                $commentaires[] = $com;
            }
            
            return $commentaires;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des commentaires : " . $e->getMessage());
            return false;
        }
    }
    
    public function getCommentaireById($id_commentaire) {
        try {
            $sql = "SELECT * FROM commentaire WHERE id_commentaire = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id_commentaire, PDO::PARAM_INT);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $com = new Commentaire();
                $com->setIdCommentaire($row['id_commentaire']);
                $com->setIdPublication($row['id_publication']);
                $com->setContenu($row['contenu']);
                $com->setAuteur($row['auteur']);
                $com->setDateCreation($row['date_creation']);
                $com->setDateModification($row['date_modification']);
                $com->setNombreLikes($row['nombre_likes']);
                return $com;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération du commentaire : " . $e->getMessage());
            return false;
        }
    }
    
    public function updateCommentaire($commentaire) {
        try {
            $sql = "UPDATE commentaire SET 
                    contenu = :contenu, 
                    date_modification = NOW()
                    WHERE id_commentaire = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':contenu', $commentaire->getContenu(), PDO::PARAM_STR);
            $stmt->bindValue(':id', $commentaire->getIdCommentaire(), PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du commentaire : " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteCommentaire($id_commentaire) {
        try {
            $this->pdo->beginTransaction();
            
            $commentaire = $this->getCommentaireById($id_commentaire);
            if (!$commentaire) {
                $this->pdo->rollBack();
                return false;
            }
            
            $id_publication = $commentaire->getIdPublication();
            
            $sql = "DELETE FROM commentaire WHERE id_commentaire = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id_commentaire, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $sqlUpdate = "UPDATE publication SET nombre_commentaires = nombre_commentaires - 1 
                             WHERE id_publication = :id";
                $stmtUpdate = $this->pdo->prepare($sqlUpdate);
                $stmtUpdate->bindValue(':id', $id_publication, PDO::PARAM_INT);
                $stmtUpdate->execute();
                
                $this->pdo->commit();
                return true;
            }
            
            $this->pdo->rollBack();
            return false;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur lors de la suppression du commentaire : " . $e->getMessage());
            return false;
        }
    }
    
    public function incrementLikes($id_commentaire) {
        try {
            $sql = "UPDATE commentaire SET nombre_likes = nombre_likes + 1 WHERE id_commentaire = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id_commentaire, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de l'incrémentation des likes : " . $e->getMessage());
            return false;
        }
    }
}
?>

