<?php
declare(strict_types=1);

final class Usuario
{
    private mysqli $db;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';

        $this->db = $conn;

        /*
            Fuerza UTF-8 completo para evitar problemas con caracteres especiales.
            No rompe consultas existentes.
        */
        if (!$this->db->set_charset('utf8mb4')) {
            error_log('Error al configurar charset utf8mb4: ' . $this->db->error);
        }
    }

    public function findByUsername(string $username): ?array
    {
        $username = trim($username);

        /*
            Protección extra:
            Evita payloads enormes en pruebas de seguridad.
        */
        if ($username === '' || mb_strlen($username) > 80) {
            return null;
        }

        $stmt = $this->db->prepare(
            "SELECT id_usuario, username, password_hash, nombre_completo, estado 
             FROM usuarios 
             WHERE username = ? 
             LIMIT 1"
        );

        if (!$stmt) {
            error_log('Error prepare findByUsername: ' . $this->db->error);
            return null;
        }

        $stmt->bind_param('s', $username);

        if (!$stmt->execute()) {
            error_log('Error execute findByUsername: ' . $stmt->error);
            $stmt->close();
            return null;
        }

        $result = $stmt->get_result();

        if (!$result) {
            error_log('Error get_result findByUsername: ' . $stmt->error);
            $stmt->close();
            return null;
        }

        $user = $result->fetch_assoc();

        $stmt->close();

        return $user ?: null;
    }

    public function updateUltimoLogin(int $id): void
    {
        if ($id <= 0) {
            return;
        }

        $stmt = $this->db->prepare(
            "UPDATE usuarios 
             SET ultimo_login = NOW() 
             WHERE id_usuario = ?"
        );

        if (!$stmt) {
            error_log('Error prepare updateUltimoLogin: ' . $this->db->error);
            return;
        }

        $stmt->bind_param('i', $id);

        if (!$stmt->execute()) {
            error_log('Error execute updateUltimoLogin: ' . $stmt->error);
        }

        $stmt->close();
    }
}