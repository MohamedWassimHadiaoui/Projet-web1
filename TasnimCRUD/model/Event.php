<?php
// model/Event.php
require_once __DIR__ . '/../config/database.php';

class Event
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getPDO();
    }

    public function all()
    {
        $stmt = $this->pdo->query('SELECT * FROM events ORDER BY date_event ASC');
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM events WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $sql = 'INSERT INTO events (title, description, date_event, type, location, participants, tags) VALUES (?, ?, ?, ?, ?, ?, ?)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['date_event'],
            $data['type'],
            $data['location'],
            $data['participants'] ?? 0,
            $data['tags'] ?? ''
        ]);
    }

    public function update($id, $data)
    {
        $sql = 'UPDATE events SET title = ?, description = ?, date_event = ?, type = ?, location = ?, participants = ?, tags = ? WHERE id = ?';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['date_event'],
            $data['type'],
            $data['location'],
            $data['participants'] ?? 0,
            $data['tags'] ?? '',
            $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM events WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
