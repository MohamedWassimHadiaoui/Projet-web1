<?php
// model/Contenu.php
require_once __DIR__ . '/../config/database.php';

class Contenu
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getPDO();
    }

    public function all()
    {
        $stmt = $this->pdo->query('SELECT * FROM contenus ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM contenus WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $sql = 'INSERT INTO contenus (title, body, author, status, tags) VALUES (?, ?, ?, ?, ?)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['title'] ?? '',
            $data['body'] ?? '',
            $data['author'] ?? null,
            $data['status'] ?? 'draft',
            $data['tags'] ?? ''
        ]);
    }

    public function update($id, $data)
    {
        $sql = 'UPDATE contenus SET title = ?, body = ?, author = ?, status = ?, tags = ? WHERE id = ?';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['title'] ?? '',
            $data['body'] ?? '',
            $data['author'] ?? null,
            $data['status'] ?? 'draft',
            $data['tags'] ?? '',
            $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM contenus WHERE id = ?');
        return $stmt->execute([$id]);
    }

    // Increment likes and return new like count (or false on failure)
    public function incrementLike($id)
    {
        $stmt = $this->pdo->prepare('UPDATE contenus SET likes = likes + 1 WHERE id = ?');
        $ok = $stmt->execute([$id]);
        if (!$ok) return false;

        $stmt2 = $this->pdo->prepare('SELECT likes FROM contenus WHERE id = ? LIMIT 1');
        $stmt2->execute([$id]);
        $row = $stmt2->fetch();
        return $row ? (int)$row['likes'] : false;
    }
}
