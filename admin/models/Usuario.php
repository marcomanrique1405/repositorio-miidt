<?php
declare(strict_types=1);

final class Usuario
{
    private mysqli $db;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';

        $this->db = $conn;

    }

    public function findByUsername(string $username): ?array
    {

        $stmt = $this->db->prepare(
            "SELECT id_usuario, username, password_hash, nombre_completo, estado 
             FROM usuarios 
             WHERE username = ? 
             LIMIT 1"
        );

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        return $user ?: null;
    }

    public function updateUltimoLogin(int $id): void
    {

        $stmt = $this->db->prepare(
            "UPDATE usuarios SET ultimo_login = NOW() WHERE id_usuario = ?"
        );

        if (!$stmt) {
            error_log("❌ Error prepare update: " . $this->db->error);
            return;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}