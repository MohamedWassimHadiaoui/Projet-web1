<?php
class Event {
    private $id;
    private $title;
    private $description;
    private $date_event;
    private $type;
    private $location;
    private $participants;
    private $tags;
    private $created_at;

    public function __construct($id = null, $title = null, $description = null, $date_event = null, $type = 'offline', $location = null, $participants = 0, $tags = null) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->date_event = $date_event;
        $this->type = $type;
        $this->location = $location;
        $this->participants = $participants;
        $this->tags = $tags;
    }

    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getDescription() { return $this->description; }
    public function getDateEvent() { return $this->date_event; }
    public function getType() { return $this->type; }
    public function getLocation() { return $this->location; }
    public function getParticipants() { return $this->participants; }
    public function getTags() { return $this->tags; }
    public function getCreatedAt() { return $this->created_at; }

    public function setId($id) { $this->id = $id; }
    public function setTitle($title) { $this->title = $title; }
    public function setDescription($description) { $this->description = $description; }
    public function setDateEvent($date_event) { $this->date_event = $date_event; }
    public function setType($type) { $this->type = $type; }
    public function setLocation($location) { $this->location = $location; }
    public function setParticipants($participants) { $this->participants = $participants; }
    public function setTags($tags) { $this->tags = $tags; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }

    public function getTagsArray() {
        if (empty($this->tags)) return [];
        return array_map('trim', explode(',', $this->tags));
    }

    public function getTypeBadge() {
        $badges = [
            'online' => 'badge-assigned',
            'offline' => 'badge-pending',
            'hybrid' => 'badge-resolved'
        ];
        return $badges[$this->type] ?? 'badge-pending';
    }

    public function getTypeLabel() {
        $labels = [
            'online' => '🌐 Online',
            'offline' => '📍 On-Site',
            'hybrid' => '🔄 Hybrid'
        ];
        return $labels[$this->type] ?? $this->type;
    }
}
?>

