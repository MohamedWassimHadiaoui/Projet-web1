<?php
require_once __DIR__ . '/config.php';
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
            
            $sqlCheck = "SELECT id_publication FROM publication WHERE id_publication = :id";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->bindValue(':id', $commentaire->getIdPublication(), PDO::PARAM_INT);
            $stmtCheck->execute();
            
            if (!$stmtCheck->fetch()) {
                $this->pdo->rollBack();
                error_log("Erreur : La publication avec l'ID " . $commentaire->getIdPublication() . " n'existe pas");
                return false;
            }
            
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
            
            $sqlGet = "SELECT id_publication FROM commentaire WHERE id_commentaire = :id";
            $stmtGet = $this->pdo->prepare($sqlGet);
            $stmtGet->bindValue(':id', $id_commentaire, PDO::PARAM_INT);
            $stmtGet->execute();
            $result = $stmtGet->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                $this->pdo->rollBack();
                return false;
            }
            
            $id_publication = $result['id_publication'];
            
            $sql = "DELETE FROM commentaire WHERE id_commentaire = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id_commentaire, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $sqlUpdate = "UPDATE publication SET nombre_commentaires = nombre_commentaires - 1 
                             WHERE id_publication = :id AND nombre_commentaires > 0";
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
    
    public function listCommentairesWithPublication() {
        try {
            $sql = "SELECT 
                        c.id_commentaire, c.id_publication, c.contenu, c.auteur, 
                        c.date_creation, c.date_modification, c.nombre_likes,
                        p.titre as pub_titre, p.categorie as pub_categorie, p.auteur as pub_auteur
                    FROM commentaire c
                    INNER JOIN publication p ON c.id_publication = p.id_publication
                    ORDER BY c.date_creation DESC";
            
            $stmt = $this->pdo->query($sql);
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
                
                $comData = array(
                    'commentaire' => $com,
                    'publication' => array(
                        'titre' => $row['pub_titre'],
                        'categorie' => $row['pub_categorie'],
                        'auteur' => $row['pub_auteur']
                    )
                );
                $commentaires[] = $comData;
            }
            
            return $commentaires;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des commentaires avec publications : " . $e->getMessage());
            return false;
        }
    }
    
    public function listCommentairesByPublicationWithJoin($id_publication) {
        try {
            $sql = "SELECT 
                        c.id_commentaire, c.id_publication, c.contenu, c.auteur, 
                        c.date_creation, c.date_modification, c.nombre_likes,
                        p.titre as pub_titre, p.contenu as pub_contenu, p.categorie as pub_categorie,
                        p.tags as pub_tags, p.auteur as pub_auteur, p.date_creation as pub_date_creation
                    FROM commentaire c
                    INNER JOIN publication p ON c.id_publication = p.id_publication
                    WHERE c.id_publication = :id
                    ORDER BY c.date_creation ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id_publication, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($results)) {
                return false;
            }
            
            $firstRow = $results[0];
            require_once dirname(__DIR__) . '/models/Publication.php';
            $pub = new Publication();
            $pub->setIdPublication($firstRow['id_publication']);
            $pub->setTitre($firstRow['pub_titre']);
            $pub->setContenu($firstRow['pub_contenu']);
            $pub->setCategorie($firstRow['pub_categorie']);
            $pub->setTags($firstRow['pub_tags']);
            $pub->setAuteur($firstRow['pub_auteur']);
            $pub->setDateCreation($firstRow['pub_date_creation']);
            
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
            
            return array(
                'publication' => $pub,
                'commentaires' => $commentaires
            );
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des commentaires avec publication : " . $e->getMessage());
            return false;
        }
    }
}
?>
