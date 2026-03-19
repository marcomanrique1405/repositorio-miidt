<?php
declare(strict_types=1);

final class Usuario
{
    private mysqli $db;

    public function __construct()
    {
        // 🔥 usar conexión existente
        require __DIR__ . '/../../config/database.php';

        $this->db = $conn;

        error_log("✅ Usando conexión global MySQLi");
    }

    public function findByUsername(string $username): ?array
    {
        error_log("🔍 Buscando usuario: " . $username);

        $stmt = $this->db->prepare(
            "SELECT id_usuario, username, password_hash, nombre_completo, estado 
             FROM usuarios 
             WHERE username = ? 
             LIMIT 1"
        );

        if (!$stmt) {
            error_log("❌ Error prepare: " . $this->db->error);
            return null;
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            error_log("✅ Usuario encontrado");
        } else {
            error_log("❌ Usuario NO encontrado");
        }

        return $user ?: null;
    }

    public function updateUltimoLogin(int $id): void
    {
        error_log("🕒 Actualizando último login ID: " . $id);

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