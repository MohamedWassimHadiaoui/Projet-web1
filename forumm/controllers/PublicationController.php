<?php
require_once __DIR__ . '/config.php';
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
    
    /**
     * Récupère une publication avec tous ses commentaires en utilisant une jointure LEFT JOIN
     * @param int $id_publication
     * @return array|false Retourne un tableau associatif avec 'publication' et 'commentaires', ou false en cas d'erreur
     */
    public function getPublicationWithCommentaires($id_publication) {
        try {
            $sql = "SELECT 
                        p.id_publication, p.titre, p.contenu, p.categorie, p.tags, 
                        p.auteur as pub_auteur, p.date_creation as pub_date_creation, 
                        p.date_modification as pub_date_modification, p.nombre_likes,
                        c.id_commentaire, c.contenu as com_contenu, c.auteur as com_auteur,
                        c.date_creation as com_date_creation, c.date_modification as com_date_modification,
                        c.nombre_likes as com_nombre_likes
                    FROM publication p
                    LEFT JOIN commentaire c ON p.id_publication = c.id_publication
                    WHERE p.id_publication = :id
                    ORDER BY c.date_creation ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id_publication, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($results)) {
                return false;
            }
            
            // Créer l'objet Publication à partir de la première ligne
            $firstRow = $results[0];
            $pub = new Publication();
            $pub->setIdPublication($firstRow['id_publication']);
            $pub->setTitre($firstRow['titre']);
            $pub->setContenu($firstRow['contenu']);
            $pub->setCategorie($firstRow['categorie']);
            $pub->setTags($firstRow['tags']);
            $pub->setAuteur($firstRow['pub_auteur']);
            $pub->setDateCreation($firstRow['pub_date_creation']);
            $pub->setDateModification($firstRow['pub_date_modification']);
            $pub->setNombreLikes($firstRow['nombre_likes']);
            
            // Créer les objets Commentaire
            require_once dirname(__DIR__) . '/models/Commentaire.php';
            $commentaires = array();
            
            foreach ($results as $row) {
                if ($row['id_commentaire'] !== null) {
                    $com = new Commentaire();
                    $com->setIdCommentaire($row['id_commentaire']);
                    $com->setIdPublication($row['id_publication']);
                    $com->setContenu($row['com_contenu']);
                    $com->setAuteur($row['com_auteur']);
                    $com->setDateCreation($row['com_date_creation']);
                    $com->setDateModification($row['com_date_modification']);
                    $com->setNombreLikes($row['com_nombre_likes']);
                    $commentaires[] = $com;
                }
            }
            
            return array(
                'publication' => $pub,
                'commentaires' => $commentaires
            );
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de la publication avec commentaires : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère toutes les publications avec le nombre réel de commentaires calculé via COUNT et LEFT JOIN
     * @return array|false Retourne un tableau d'objets Publication avec le nombre réel de commentaires, ou false en cas d'erreur
     */
    public function listPublicationsWithCommentCount() {
        try {
            $sql = "SELECT 
                        p.id_publication, p.titre, p.contenu, p.categorie, p.tags, 
                        p.auteur, p.date_creation, p.date_modification, p.nombre_likes,
                        COUNT(c.id_commentaire) as nombre_commentaires_reel
                    FROM publication p
                    LEFT JOIN commentaire c ON p.id_publication = c.id_publication
                    GROUP BY p.id_publication, p.titre, p.contenu, p.categorie, p.tags, 
                             p.auteur, p.date_creation, p.date_modification, p.nombre_likes
                    ORDER BY p.date_creation DESC";
            
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
                $pub->setNombreCommentaires($row['nombre_commentaires_reel']);
                $publications[] = $pub;
            }
            
            return $publications;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des publications avec nombre de commentaires : " . $e->getMessage());
            return false;
        }
    }
}
?>
