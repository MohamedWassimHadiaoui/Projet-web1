<?php
class Contenu {
    private $id;
    private $title;
    private $body;
    private $author;
    private $status;
    private $likes;
    private $tags;
    private $created_at;
    private $updated_at;

    public function __construct($id = null, $title = null, $body = null, $author = null, $status = 'draft', $tags = null) {
        $this->id = $id;
        $this->title = $title;
        $this->body = $body;
        $this->author = $author;
        $this->status = $status;
        $this->tags = $tags;
        $this->likes = 0;
    }

    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getBody() { return $this->body; }
    public function getAuthor() { return $this->author; }
    public function getStatus() { return $this->status; }
    public function getLikes() { return $this->likes; }
    public function getTags() { return $this->tags; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }

    public function setId($id) { $this->id = $id; }
    public function setTitle($title) { $this->title = $title; }
    public function setBody($body) { $this->body = $body; }
    public function setAuthor($author) { $this->author = $author; }
    public function setStatus($status) { $this->status = $status; }
    public function setLikes($likes) { $this->likes = $likes; }
    public function setTags($tags) { $this->tags = $tags; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }
    public function setUpdatedAt($updated_at) { $this->updated_at = $updated_at; }

    public function getTagsArray() {
        if (empty($this->tags)) return [];
        return array_map('trim', explode(',', $this->tags));
    }

    public function getStatusBadge() {
        $badges = [
            'draft' => 'badge-pending',
            'published' => 'badge-resolved',
            'archived' => 'badge-closed'
        ];
        return $badges[$this->status] ?? 'badge-pending';
    }

    public function getStatusLabel() {
        $labels = [
            'draft' => '📝 Draft',
            'published' => '✅ Published',
            'archived' => '📦 Archived'
        ];
        return $labels[$this->status] ?? $this->status;
    }
}
?>

