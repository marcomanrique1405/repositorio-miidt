<?php
declare(strict_types=1);

final class Dashboard
{
    private mysqli $db;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->db = $conn;
    }

    public function totalTesis(): int
    {
        $result = $this->db->query("SELECT COUNT(*) AS total FROM tesis");
        $row = $result->fetch_assoc();
        return (int) ($row['total'] ?? 0);
    }

    public function totalDirectores(): int
    {
        $result = $this->db->query("SELECT COUNT(*) AS total FROM director");
        $row = $result->fetch_assoc();
        return (int) ($row['total'] ?? 0);
    }

    
    public function totalFisico(): int
    {
        $result = $this->db->query("
            SELECT COUNT(*) AS total 
            FROM tesis 
            WHERE estado LIKE '%Fisico%'
        ");

        $row = $result->fetch_assoc();
        return (int) ($row['total'] ?? 0);
    }

    
    public function totalDigital(): int
    {
        $result = $this->db->query("
            SELECT COUNT(*) AS total 
            FROM tesis 
            WHERE estado LIKE '%Digital%'
        ");

        $row = $result->fetch_assoc();
        return (int) ($row['total'] ?? 0);
    }
}