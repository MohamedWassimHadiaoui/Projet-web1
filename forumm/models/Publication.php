<?php
class Publication {
    private $id_publication;
    private $titre;
    private $contenu;
    private $categorie;
    private $tags;
    private $auteur;
    private $date_creation;
    private $date_modification;
    private $nombre_likes;
    private $nombre_commentaires;
    
    public function __construct() {
    }
    
    public function getIdPublication() {
        return $this->id_publication;
    }
    
    public function getTitre() {
        return $this->titre;
    }
    
    public function getContenu() {
        return $this->contenu;
    }
    
    public function getCategorie() {
        return $this->categorie;
    }
    
    public function getTags() {
        return $this->tags;
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
    
    public function getNombreCommentaires() {
        return $this->nombre_commentaires;
    }
    
    public function setIdPublication($id_publication) {
        $this->id_publication = $id_publication;
    }
    
    public function setTitre($titre) {
        $this->titre = $titre;
    }
    
    public function setContenu($contenu) {
        $this->contenu = $contenu;
    }
    
    public function setCategorie($categorie) {
        $this->categorie = $categorie;
    }
    
    public function setTags($tags) {
        $this->tags = $tags;
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
    
    public function setNombreCommentaires($nombre_commentaires) {
        $this->nombre_commentaires = $nombre_commentaires;
    }
}
?>

