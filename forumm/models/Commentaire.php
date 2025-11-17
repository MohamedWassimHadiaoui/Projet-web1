<?php
class Commentaire {
    private $id_commentaire;
    private $id_publication;
    private $contenu;
    private $auteur;
    private $date_creation;
    private $date_modification;
    private $nombre_likes;
    
    public function __construct() {
    }
    
    public function getIdCommentaire() {
        return $this->id_commentaire;
    }
    
    public function getIdPublication() {
        return $this->id_publication;
    }
    
    public function getContenu() {
        return $this->contenu;
    }
    
    public function getAuteur() {
        return $this->auteur;
    }
    
    public function getDateCreation() {
        return $this->date_creation;
    }
    
    public function getDateModification() {
        return $this->date_modification;
    }
    
    public function getNombreLikes() {
        return $this->nombre_likes;
    }
    
    public function setIdCommentaire($id_commentaire) {
        $this->id_commentaire = $id_commentaire;
    }
    
    public function setIdPublication($id_publication) {
        $this->id_publication = $id_publication;
    }
    
    public function setContenu($contenu) {
        $this->contenu = $contenu;
    }
    
    public function setAuteur($auteur) {
        $this->auteur = $auteur;
    }
    
    public function setDateCreation($date_creation) {
        $this->date_creation = $date_creation;
    }
    
    public function setDateModification($date_modification) {
        $this->date_modification = $date_modification;
    }
    
    public function setNombreLikes($nombre_likes) {
        $this->nombre_likes = $nombre_likes;
    }
}
?>

